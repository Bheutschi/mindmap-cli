<?php

namespace MindMap\Command;

use JsonException;
use MindMap\Model\MindMap;
use MindMap\Model\Node;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class MindMapCommand extends Command
{
    private SymfonyStyle $io;

    public function __construct(
        public MindMap $mindMap,
        public Node    $node,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('create');
        $this->setDescription('Créer une mind map');
        $this->setHelp('Cette commande permet de créer une mind map');
    }

    /**
     * @throws JsonException
     * Commande principale pour les lignes de commandes
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $rep = dirname(__DIR__) . '/data';


        $this->io = new SymfonyStyle($input, $output);

        do {
            $choice = $this->io->choice(
                'Que voulez-vous faire ?',
                [
                    'Créer une carte',
                    'Ajouter un nœud',
                    'Ajouter un enfant',
                    'Supprimer une carte',
                    'Supprimer un nœud',
                    'Afficher une carte',
                    'Quitter'
                ],
                'Créer une carte'
            );

            switch ($choice) {
                case 'Créer une carte':
                    $this->handleCreateMap();
                    break;
                case 'Ajouter un nœud':
                    $this->handleAddNode($input, $output);
                    break;
                case 'Ajouter un enfant':
                    $this->handleAddChildNode($input, $output);
                    break;
                case 'Supprimer une carte':
                    $this->handleDeleteMap($input, $output);
                    break;
                case 'Supprimer un nœud':
                    $this->handleDeleteNode($input, $output);
                    break;
                case 'Afficher une carte':
                    $this->handleDisplayMap($input, $output);
                    break;
            }
        } while ($choice !== 'Quitter');

        return Command::SUCCESS;
    }

    /**
     * Crée une carte
     * @throws JsonException
     */
    private function handleCreateMap(): void
    {
        $title = $this->io->ask('Quel est le titre de la carte ?');
        $this->mindMap->setTitle($title);
        if ($this->mindMap->saveMap()) {
            $this->io->success('Carte créée avec succès !');
        } else {
            $this->io->error('Une carte avec ce titre existe déjà. Veuillez choisir un autre titre. Ou supprimer la carte existante.');
        }
    }

    /**
     * Ajoute un nœud
     * @throws JsonException
     */
    private function handleAddNode(InputInterface $input, OutputInterface $output): void
    {
        $choix = $this->selectMapFile($input, $output);
        if ($choix === null) {
            return;
        }

        $this->node->setTitleFile($choix);
        $title = $this->io->ask('Quel est le titre du nœud ?');
        $this->node->setTitle($title);
        $this->node->addNode();
    }

    private function selectMapFile(InputInterface $input, OutputInterface $output): ?string
    {
        $helper = $this->getHelper('question');
        $rep = dirname(__DIR__) . '/data';

        $finder = new Finder();
        $finder->files()->in($rep);
        $noms = [];

        foreach ($finder as $file) {
            $noms[] = $file->getFilename();
        }

        if (empty($noms)) {
            $this->io->error('Aucune carte trouvée. Veuillez en créer une.');
            return null;
        }

        $question = new ChoiceQuestion(
            'Sélectionnez un fichier',
            $noms,
        );

        $choix = $helper->ask($input, $output, $question);
        $output->writeln(sprintf('Vous avez choisi : %s', $choix));

        return $choix;
    }

    /**
     * Ajoute un enfant à un nœud
     * @throws JsonException
     */
    private function handleAddChildNode(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $choix = $this->selectMapFile($input, $output);
        if ($choix === null) {
            return;
        }

        $this->node->setTitleFile($choix);
        $questionOfNode = new ChoiceQuestion(
            'Sélectionnez un noeud',
            $this->node->arrayOfNode(),
        );
        $choiceOfNode = $helper->ask($input, $output, $questionOfNode);

        $parentId = $this->node->getParentId($choiceOfNode);
        $this->node->setId();
        $title = $this->io->ask('Quel est le titre de l\'enfant ?');
        $this->node->setTitle($title);
        $this->node->addChildFromId($parentId);
    }

    /**
     * Supprime un enfant
     */
    private function handleDeleteMap(InputInterface $input, OutputInterface $output): void
    {
        $choix = $this->selectMapFile($input, $output);
        if ($choix === null) {
            return;
        }
        $this->mindMap->setTitle($choix);
        $this->mindMap->deleteMap();
    }

    // Sélectionne un fichier de carte

    /**
     * Supprime un noeud
     * @throws JsonException
     */
    private function handleDeleteNode(InputInterface $input, OutputInterface $output): void
    {
        $helper = $this->getHelper('question');
        $choix = $this->selectMapFile($input, $output);
        if ($choix === null) {
            return;
        }
        $this->node->setTitleFile($choix);
        $questionOfNode = new ChoiceQuestion(
            'Sélectionnez un noeud à supprimer',
            $this->node->arrayOfNode(),
        );
        $choiceOfNode = $helper->ask($input, $output, $questionOfNode);
        $idNode = $this->node->getIdOfNodeByTitle($choiceOfNode);
        $this->node->deleteNodeById($idNode);
    }

    // Logique pour construire l'arbre à afficher dans la console

    /**
     * Affiche la carte sous forme d'arbre
     * @throws JsonException
     */
    private function handleDisplayMap(InputInterface $input, OutputInterface $output): void
    {
        $choix = $this->selectMapFile($input, $output);
        if ($choix === null) {
            return;
        }
        $path = dirname(__DIR__) . '/data/' . $choix;
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $this->printTree($data);
    }

    private function printTree(array $node, string $prefix = '', string $childPrefix = ''): void
    {
        $this->io->writeln($prefix . ($node['Titre'] ?? '[sans titre]'));

        $children = $node['Noeud'] ?? ($node['Noeud enfant'] ?? []);
        $count = count($children);

        foreach ($children as $i => $child) {
            $isLast = ($i === $count - 1);
            $newLabelPrefix = $childPrefix . ($isLast ? '└─ ' : '├─ ');
            $newChildPrefix = $childPrefix . ($isLast ? '   ' : '│  ');
            $this->printTree($child, $newLabelPrefix, $newChildPrefix);
        }
    }
}