import { Command } from 'commander';
import fs from 'fs';
import path from 'path';
import seedrandom from 'seedrandom';

const program = new Command();

program
  .name('qanil-cli')
  .description('CLI to generate and manage Crónicas de Q’anil game data')
  .version('1.0.0');

program
  .command('generate-region')
  .option('-d, --department <type>', 'Department name', 'Zacapa')
  .option('-s, --seed <type>', 'Seed for procedural generation')
  .action((options) => {
    const { department, seed } = options;
    const finalSeed = seed || Math.random().toString(36).substring(7);
    const rng = seedrandom(finalSeed);

    console.log(`Generating region: ${department} with seed: ${finalSeed}`);

    // Load base region template
    const templatePath = path.join(process.cwd(), 'data', 'regions', `${department.toLowerCase()}.json`);
    if (!fs.existsSync(templatePath)) {
      console.error(`Error: Template for ${department} not found at ${templatePath}`);
      return;
    }

    const template = JSON.parse(fs.readFileSync(templatePath, 'utf-8'));
    const mechanicsPath = path.join(process.cwd(), 'data', 'systems', 'world_mechanics.json');
    const mechanics = JSON.parse(fs.readFileSync(mechanicsPath, 'utf-8'));

    // Procedural variations based on seed and real biomes
    const weather = template.climate;
    const weatherEffect = mechanics.weatherEffects[weather] || mechanics.weatherEffects['templado'];

    const generatedRegion = {
      ...template,
      seed: finalSeed,
      generatedAt: new Date().toISOString(),
      proceduralData: {
        enemyDensity: rng() > 0.5 ? 'high' : 'low',
        resourceAbundance: rng() > 0.5 ? 'rich' : 'scarce',
        currentWeather: weather,
        weatherImpact: weatherEffect,
        marketModifiers: mechanics.tradeLogic.regionalSpecialties[department.toLowerCase()] ? 'abundance' : 'normal'
      }
    };

    const outputPath = path.join(process.cwd(), 'data', 'regions', `${department.toLowerCase()}_generated.json`);
    fs.writeFileSync(outputPath, JSON.stringify(generatedRegion, null, 2));
    console.log(`Region generated successfully: ${outputPath}`);
  });

program
  .command('generate-quests')
  .option('-r, --region <type>', 'Region name', 'Zacapa')
  .option('-c, --count <number>', 'Number of quests to generate', '5')
  .option('-s, --seed <type>', 'Seed for generation')
  .action((options) => {
    const { region, count, seed } = options;
    const finalSeed = seed || Math.random().toString(36).substring(7);
    const rng = seedrandom(finalSeed);
    const questCount = parseInt(count);

    console.log(`Generating ${questCount} quests for ${region} with seed: ${finalSeed}`);

    const templatesPath = path.join(process.cwd(), 'data', 'systems', 'procedural-quests.json');
    const templates = JSON.parse(fs.readFileSync(templatesPath, 'utf-8'));

    const generatedQuests = [];
    for (let i = 0; i < questCount; i++) {
      const template = templates[Math.floor(rng() * templates.length)];
      let title = template.titleTemplate;

      if (template.type === 'hunt') {
        const animal = template.possibleAnimals[Math.floor(rng() * template.possibleAnimals.length)];
        title = title.replace('{animal}', animal).replace('{village}', 'Santiago');
      } else if (template.type === 'gather') {
        const resource = template.possibleResources[Math.floor(rng() * template.possibleResources.length)];
        title = title.replace('{resource}', resource);
      }

      generatedQuests.push({
        id: `sq_${region.toLowerCase()}_${i + 1}`,
        title,
        type: template.type,
        status: 'available',
        rewards: template.rewards
      });
    }

    const outputPath = path.join(process.cwd(), 'data', 'quests', `side-quests-${region.toLowerCase()}.json`);
    fs.writeFileSync(outputPath, JSON.stringify(generatedQuests, null, 2));
    console.log(`Quests generated: ${outputPath}`);
  });

program.parse();
