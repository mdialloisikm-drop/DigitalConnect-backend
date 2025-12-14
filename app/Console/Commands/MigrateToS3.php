<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateToS3 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate-to-s3 {--dry-run : Simule la migration sans transférer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migre les fichiers de local vers S3';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('🔍 Mode simulation activé - Aucun fichier ne sera transféré');
        }

        // Récupérer tous les fichiers du storage local
        $localDisk = Storage::disk('public');
        $s3Disk = Storage::disk('s3');

        // Récupérer TOUS les fichiers récursivement
        $allFiles = $localDisk->allFiles();

        if (empty($allFiles)) {
            $this->warn('⚠️  Aucun fichier trouvé dans le storage local');
            return 0;
        }

        $totalFiles = count($allFiles);
        $successCount = 0;
        $errorCount = 0;

        $this->info("\n📁 Migration de tous les fichiers ({$totalFiles} fichiers)");

        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        foreach ($allFiles as $file) {
            try {
                if (!$isDryRun) {
                    // Lire le contenu du fichier local
                    $content = $localDisk->get($file);

                    // Copier vers S3 (sans ACL)
                    $s3Disk->put($file, $content);

                    // Vérifier que le fichier existe sur S3
                    if (!$s3Disk->exists($file)) {
                        throw new \Exception("Échec de la vérification");
                    }
                }

                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("❌ Erreur pour $file: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Résumé
        $this->info('📊 Résumé de la migration:');
        $this->table(
            ['Statut', 'Nombre'],
            [
                ['Total', $totalFiles],
                ['Succès', $successCount],
                ['Erreurs', $errorCount],
            ]
        );

        if ($isDryRun) {
            $this->newLine();
            $this->info('✅ Simulation terminée. Lancez sans --dry-run pour effectuer la migration réelle.');
        } elseif ($errorCount === 0) {
            $this->newLine();
            $this->info('✅ Migration terminée avec succès!');
            $this->warn('⚠️  N\'oubliez pas de mettre à jour votre code pour utiliser le disque S3');
        }

        return $errorCount === 0 ? 0 : 1;
    }
}
