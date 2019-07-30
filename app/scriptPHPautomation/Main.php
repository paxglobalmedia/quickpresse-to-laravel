<?php

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Node\Stmt;
use PhpParser\{Node, NodeFinder};
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\NameContext;

use App\scriptPHPautomation\CheckIfFolderExist;
use App\scriptPHPautomation\detectIfileExist;
use App\scriptPHPautomation\getCurrentPath;
use App\scriptPHPautomation\getLineNumber
use App\scriptPHPautomation\renameClass ;
use App\scriptPHPautomation\searchClassDeclaration;
use App\scriptPHPautomation\toMoveFileClass;
use App\scriptPHPautomation\toRenameNameSpace;


namespace App\scriptPHPautomation;


use PhpParser\NameContext;
use PhpParser\NodeFinder;

/**
 * Class Main to automate move dependecy class use by plugin from OctoberCMS to Laravel
 * @package App\scriptPHPautomation
 */
class Main
{
    /**
     * Main constructor.
     */
    public function __construct($pluginDirectoryName, $pluginName)
    {
        // The developer insert the plugin directory name in popup form
        $this->pluginDirectoryName = $pluginDirectoryName;

        // The developer insert the plugin name in popup form
        $this->pluginName = $pluginName;

        // standard october path;
        $this->octoberpluginPath = '/plugins';

        // The full current plugin to convert to laravel
        $this->pluginFullPath = $this->octoberpluginPath.'/'.$this->pluginDirectoryName.'/'.$this->pluginName;

        // Create an objet from php-parser to find Node like class
        $this->nodeFinder = new NodeFinder;

        // Create an objet from php-parser to find Node NameSpace
        $this->nameSpace = new NameContext;

    }

    /**
     * @param $pluginDirectoryName
     * @param $pluginName
     */
    public function automation(){

        //Create Folder Class in App/$PluginName/
        new CheckIfFolderExist('app/'.$this->pluginName.'/class');

        //Create The Folder of the plugin in Laravel if not exist
       $this->checkFolderExistence($this->pluginFullPath);

        //Search get Parent Class for current class if exist one
        $this->getAllParentClasses($className=null);

        // Search Class Declaration and move it to App/$pluginNAme/Class
        //Get The full dependency class
        $this->getAllClassesExtendAnother($className=null);

    }

    /**
     * @param $pluginFullPath
     * @return CheckIfFolderExist
     */
    public function checkFolderExistence($pluginFullPath){

        $checkFolder = new CheckIfFolderExist($pluginFullPath);
        return $checkFolder ;
    }


    /**
     * Find all class nodes.
     * @param $stmts
     * @return array|\PhpParser\Node[]
     */
    public function getAllClasses($stmts){

        $classes = $this->nodeFinder->findInstanceOf($stmts, Node\Stmt\Class_::class);
        return $classes;
    }

    /**
     * Find all classes that extend another class
     * @param $stmts
     * @return array|\PhpParser\Node[]
     */
    public function getAllClassesExtendAnother($stmts)
    {
        $extendingClasses = $this->nodeFinder->find($stmts, function(Node $node) {
            return $node instanceof Node\Stmt\Class_
                && $node->extends !== null;
        });
        return $extendingClasses;
    }

    /**
     * Find first class that has name $name
     * @param $stmts
     * @param $name
     * @return \PhpParser\Node|null
     */
    public function getAClassbyNAme($name, $stmts)
    {
        $class = $this->nodeFinder->findFirst($stmts, function(Node $node) use ($name) {
            return $node instanceof Node\Stmt\Class_
                && $node->resolvedName->toString() === $name;
        });

        return $class;
    }

    /**
     * Start a new namespace.
     * This also resets the alias table.
     * @param \PhpParser\Node\Name|null $namespace
     * @return mixed
     */
    public function createNewNameSpace(Name $namespace = null)
    {
        $newNameSpace = $this->nameSpace->startNamespace($namespace);
        return $newNameSpace;
    }

    /**
     * Get current namespace.
     * @return null|Name Namespace (or null if global namespace)
     */
    public function getCurrentNameSpace()
    {
        $currentNameSpace = $this->nameSpace->getNamespace();
        return $currentNameSpace;
    }

    /**
     * @param null $className
     * @return string
     */
    public function getParentClass($className=null) :string
    {
        $theClass = get_parent_class($className);
        return $theClass ;
    }

    /**
     * @param null $className
     * @return array
     */
    public function getAllParentClasses($className=null) :array
    {
        $allParents = class_parents(new $className);
        return $allParents;
    }

}