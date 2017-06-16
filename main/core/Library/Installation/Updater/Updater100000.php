<?php
/**
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Date: 3/1/17
 */

namespace Claroline\CoreBundle\Library\Installation\Updater;

use Claroline\CoreBundle\DataFixtures\PostInstall\Data\PostLoadRolesData;
use Claroline\InstallationBundle\Updater\Updater;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

class Updater100000 extends Updater
{
    private $container;
    protected $logger;
    private $om;

    public function __construct(ContainerInterface $container, $logger = null)
    {
        $this->container = $container;
        $this->logger = $logger;
        $this->om = $container->get('claroline.persistence.object_manager');
        $this->connection = $this->container->get('doctrine.dbal.default_connection');
    }

    public function postUpdate()
    {
        $this->installCore();
        $this->setResourceNodeProperties();
        $this->rebuildMaskAndMenus();
        $this->enableWorkspaceList();
        $this->moveUploadsDirectory();
    }

    public function enableWorkspaceList()
    {
        $this->log('Enable workspace list...');
        $fixtures = new PostLoadRolesData();
        $fixtures->setContainer($this->container);
        $fixtures->load($this->om);
    }

    public function installCore()
    {
        $plugin = $this->om->getRepository('ClarolineCoreBundle:Plugin')->findOneBy([
          'vendorName' => 'Claroline', 'bundleName' => 'CoreBundle',
        ]);
        if (!$plugin) {
            $this->log('Persisting CoreBundle...');
            $plugin = new Plugin();
            $plugin->setBundleName('CoreBundle');
            $plugin->setVendorName('Claroline');
            $this->persist($plugin);
            $this->flush();
        } else {
            $this->log('CoreBundle already installed');
        }
    }

    public function setResourceNodeProperties()
    {
        $entities = $this->om->getRepository('ClarolineCoreBundle:Resource\ResourceNode')->findAll();
        $totalObjects = count($entities);
        $i = 0;
        $this->log("Adding properties for {$totalObjects} resource nodes...");

        $this->connection->query('
            UPDATE claro_resource_node crn
            SET crn.fullscreen = false
            WHERE crn.fullscreen is NULL'
        )->execute();

        $this->connection->query('
            UPDATE claro_resource_node crn
            SET crn.closable = false
            WHERE crn.closable is NULL'
        )->execute();

        $this->connection->query('
            UPDATE claro_resource_node crn
            SET crn.closeTarget = 0
            WHERE crn.closeTarget is NULL'
        )->execute();
    }

    public function rebuildMaskAndMenus()
    {
        $this->log('Removing old menus and masks...');
        $classes = ['ClarolineCoreBundle:Resource\MaskDecoder', 'ClarolineCoreBundle:Resource\MenuAction'];

        foreach ($classes as $class) {
            $entities = $this->om->getRepository($class)->findAll();

            foreach ($entities as $entity) {
                $this->om->remove($entity);
            }
        }

        $this->om->flush();

        $this->log('Building new menus and masks');
        $this->container->get('claroline.plugin.installer')->setLogger($this->logger);
        $this->container->get('claroline.plugin.installer')->updateAllConfigurations();
        $this->log('On older plateforms, resource permissions might have changed !');
    }

    public function moveUploadsDirectory()
    {
        $this->log('Moving file storage directories...');
        $toMove = ['uploads', 'themes'];

        $fs = new FileSystem();

        foreach ($toMove as $directory) {
            if ($this->logger) {
                $this->log('Moving '.$directory.'...');
            }

            try {
                $fs->rename(
                  $this->container->getParameter('claroline.param.web_dir').DIRECTORY_SEPARATOR.$directory,
                  $this->container->getParameter('claroline.param.data_web_dir').DIRECTORY_SEPARATOR.$directory
              );
            } catch (IOException $e) {
                if ($this->logger) {
                    $this->log('Directory '.$this->container->getParameter('claroline.param.data_web_dir').DIRECTORY_SEPARATOR.$directory.' already exists...');
                }
            }

            $fs->remove($this->container->getParameter('claroline.param.web_dir').DIRECTORY_SEPARATOR.$directory);

            $fs->symlink(
                $this->container->getParameter('claroline.param.data_web_dir').DIRECTORY_SEPARATOR.$directory,
                $this->container->getParameter('claroline.param.web_dir').DIRECTORY_SEPARATOR.$directory
            );
        }
    }
}
