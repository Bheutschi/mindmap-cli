<?php

namespace MindMap\Model;



class MindMap
{
    public string $title;
    public array $node = [];
    
    
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
    public function getTitle(): string
    {
        return $this->title;
    }
    
    public function returnMap(): array
    {
        return [
            'Titre' => $this->title,
            'Noeud' => $this->node,
        ];
    }

    /**
     * @throws \JsonException
     */
    public function saveMap(): bool
    {
        
        $filePath = dirname(__DIR__) . '/data/'. strtolower($this->title) . '.json';

        if (file_exists($filePath)) {
            return false;
        }
        file_put_contents($filePath, json_encode($this->returnMap(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT ));
        return true;
    }

    public function deleteMap(): void
    {
        $filePath = dirname(__DIR__) . '/data/'. strtolower($this->title);
        unlink(realpath($filePath));
    }



}