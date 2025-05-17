<?php

namespace MindMap\Model;

use JsonException;

class Node extends MindMap
{
    private int $id;
    private string $titleFile;

    public function __construct()
    {
        $this->id = 0;
        $this->titleFile = dirname(__DIR__) . '/data/test.json';
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @throws JsonException
     */
    public function setId(): void
    {
        $filename = $this->getFile();
        $json = file_get_contents($filename);
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->id = $data['Noeud'] === null ? 1 : count($data['Noeud']) + 1;
    }

    public function setTitleFile(string $titleFile): void
    {
        $this->titleFile = dirname(__DIR__) . '/data/' . $titleFile;
    }

    /**
     * @throws JsonException
     */
    public function arrayOfNode(): array
    {
        $data = $this->loadJsonData();
        $nodeTitles = [];

        foreach ($data['Noeud'] as $title) {
            $nodeTitles[] = $title['Titre'];
        }
        return $nodeTitles;
    }

    /**
     * @throws JsonException
     */
    private function loadJsonData(): array
    {
        $filename = $this->getFile();
        $json = file_get_contents($filename);
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    public function getFile(): string
    {
        return $this->titleFile;
    }

    /**
     * @throws JsonException
     */
    public function getIdOfNodeByTitle($title): ?int
    {
        $data = $this->loadJsonData();

        foreach ($data['Noeud'] as $node) {
            if ($node['Titre'] === $title) {
                return $node['id'];
            }
        }
        return null;
    }

    /**
     * @throws JsonException
     */
    public function getChildNodes(int $parentId): array
    {
        $data = $this->loadJsonData();

        $children = [];

        foreach ($data['Noeud'] as $node) {
            if ($node['id'] === $parentId && isset($node['Noeud enfant'])) {
                foreach ($node['Noeud enfant'] as $child) {
                    $children[$child['id']] = $child['Titre'] . ' (ID: ' . $child['id'] . ')';
                }
                break;
            }
        }

        return $children;
    }

    /**
     * @throws JsonException
     */
    public function getParentId($title)
    {
        $data = $this->loadJsonData();
        foreach ($data['Noeud'] as $id) {
            if ($id['Titre'] === $title) {
                return $id['id'];
            }
        }
        return null;
    }

    /**
     * @throws JsonException
     */
    public function addChildFromId($id): void
    {

        $data = $this->loadJsonData();

        foreach ($data['Noeud'] as $key => $idNode) {
            if ($idNode['id'] === $id) {
                $numberOfChild = count($data['Noeud'][$key]['Noeud enfant']);
                $nextIndex = $numberOfChild + 1;
                $data['Noeud'][$key]['Noeud enfant'][] = [
                    'id' => $nextIndex,
                    'Titre' => $this->title,
                ];
                $this->saveJsonData($data);
            }
        }
    }

    /**
     * @throws JsonException
     */
    private function saveJsonData(array $data): void
    {
        $filename = $this->getFile();
        file_put_contents(
            $filename,
            json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    /**
     * @throws JsonException
     */
    public function addNode(): void
    {

        $data = $this->loadJsonData();

        $this->setId();

        if ($data['Noeud'] === null) {
            $data['Noeud'] = $this->returnNode();
        }

        $data['Noeud'][] = $this->returnNode();

        $this->saveJsonData($data);
    }

    public function returnNode(): array
    {
        return [
            'id' => $this->id,
            'Titre' => $this->title,
            'Noeud enfant' => $this->node,
        ];
    }

    /**
     *
     * @throws JsonException
     */
    public function deleteNodeById($nodeId): void
    {
        $data = $this->loadJsonData();

        foreach ($data['Noeud'] as $index => $node) {
            if ($node['id'] === $nodeId) {
                unset($data['Noeud'][$index]);
                break;
            }
        }
        $data['Noeud'] = array_values($data['Noeud']);

        $this->saveJsonData($data);
    }

}