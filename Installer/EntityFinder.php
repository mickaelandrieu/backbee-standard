<?php

namespace BackBuilder\Installer;

use BackBuilder\Util\Dir;
use Doctrine\Common\Annotations\SimpleAnnotationReader,
    Doctrine\ORM\Mapping\Entity;

class EntityFinder
{

    /**
     * @var array
     */
    private $_ignoredFolder = array(
        'Resources',
        'Ressources',
        'TestUnit',
        'Exception'
    );

    /**
     * @var string
     */
    private $_baseDir;

    /**
     * @var SimpleAnnotationReader
     */
    private $_annotationReader;

    public function __construct($baseDir)
    {
        $this->_baseDir = $baseDir;
    }

    /**
     * @param string $path
     * @return array
     */
    public function getEntities($path)
    {
        $entities = array();
        foreach (Dir::getContent($path) as $content) {
            $subpath = $path . DIRECTORY_SEPARATOR . $content;
            if (in_array($content, $this->_ignoredFolder))
                continue;
            if (is_dir($subpath)) {
                $entities = array_merge($entities, $this->getEntities($subpath));
            } else {
                if (strpos($subpath, '.php')) {
                    $namespace = $this->getNamespace($subpath);
                    if ($this->_isEntity(new \ReflectionClass($namespace))) {
                        $entities[] = $namespace;
                    }
                }
            }
        }
        return $entities;
    }

    /**
     * @param string $file
     * @return string
     */
    public function getNamespace($file)
    {
        $classname = str_replace(array($this->_baseDir, 'bundle', '.php', '/'), array('', 'Bundle', '', '\\'), $file);
        return (strpos($classname, 'BackBuilder') === false) ? 'BackBuilder' . $classname : $classname;
    }

    /**
     * @param \ReflectionClass $reflection
     * @return boolean
     */
    private function _isEntity(\ReflectionClass $reflection)
    {
        return !is_null($this->_getEntityAnnotation($reflection));
    }

    /**
     * @param \ReflectionClass $class
     * @return Entity
     */
    private function _getEntityAnnotation(\ReflectionClass $class)
    {
        if (!$this->_annotationReader) {
            $this->_annotationReader = new SimpleAnnotationReader();
            $this->_annotationReader->addNamespace('Doctrine\ORM\Mapping');
        }
        return $this->_annotationReader->getClassAnnotation($class, new Entity());
    }

}