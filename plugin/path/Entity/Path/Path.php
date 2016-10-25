<?php

namespace Innova\PathBundle\Entity\Path;

use Claroline\CoreBundle\Entity\Resource\AbstractResource;
use Claroline\CoreBundle\Entity\Resource\ResourceNode;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Innova\PathBundle\Entity\Step;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Path.
 *
 * @ORM\Table(name="innova_path")
 * @ORM\Entity(repositoryClass="Innova\PathBundle\Repository\PathRepository")
 */
class Path extends AbstractResource implements PathInterface, \JsonSerializable
{
    /**
     * Name of the path (only for forms).
     *
     * @var string
     *
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * JSON structure of the path.
     *
     * @var string
     *
     * @ORM\Column(name="structure", type="text")
     */
    protected $structure;

    /**
     * Display a breadcrumbs for navigation into the Path.
     *
     * @var bool
     *
     * @ORM\Column(name="breadcrumbs", type="boolean")
     */
    protected $breadcrumbs = true;

    /**
     * Is the summary displayed when open the Player ?
     *
     * @var bool
     *
     * @ORM\Column(name="summary_displayed", type="boolean")
     */
    protected $summaryDisplayed = true;

    /**
     * Steps linked to the path.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Innova\PathBundle\Entity\Step", mappedBy="path", indexBy="id", cascade={"persist", "remove"})
     * @ORM\OrderBy({"lvl" = "ASC", "order" = "ASC"})
     */
    protected $steps;

    /**
     * @var bool
     *
     * @ORM\Column(name="modified", type="boolean")
     */
    protected $modified;

    /**
     * Description of the path.
     *
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * Does the path manage conditions that block all the further steps or just the next one.
     *
     * @var bool
     *
     * @ORM\Column(name="is_complete_blocking_condition", type="boolean")
     */
    protected $completeBlockingCondition;

    /**
     * Is it possible for the user to manualy set the progression.
     *
     * @var bool
     *
     * @ORM\Column(name="manual_progression_allowed", type="boolean")
     */
    protected $manualProgressionAllowed;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->steps = new ArrayCollection();
        $this->modified = false;
        $this->completeBlockingCondition = true;
        $this->manualProgressionAllowed = true;
    }

    /**
     * Set json structure.
     *
     * @param string $structure
     *
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * Get a JSON version of the Path.
     *
     * @return string
     */
    public function getStructure()
    {
        $isPublished = $this->isPublished();
        if ($isPublished && !$this->modified) {
            // Rebuild the structure of the Path from generated data
            // This permits to merge modifications made on generated data into the Path
            $structure = json_encode($this); // See `jsonSerialize` to know how it's populated
        } else {
            // There are unpublished data so get the structure generated by the editor
            $structure = $this->structure;
        }

        return $structure;
    }

    /**
     * Is path already published.
     *
     * @return bool
     */
    public function isPublished()
    {
        if ($this->resourceNode instanceof ResourceNode) {
            return $this->resourceNode->isPublished();
        } else {
            return false;
        }
    }

    public function setPublished($published)
    {
        $this->resourceNode->setPublished($published);

        return $this;
    }

    /**
     * Set modified.
     *
     * @param bool $modified
     *
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Is path modified since the last deployment.
     *
     * @return bool
     */
    public function isModified()
    {
        return $this->modified;
    }

    /**
     * Set summary displayed.
     *
     * @param bool $displayed
     *
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function setSummaryDisplayed($displayed)
    {
        $this->summaryDisplayed = $displayed;

        return $this;
    }

    /**
     * Is summary displayed when open Player ?
     *
     * @return bool
     */
    public function isSummaryDisplayed()
    {
        return $this->summaryDisplayed;
    }

    /**
     * Add step.
     *
     * @param \Innova\PathBundle\Entity\Step $step
     *
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function addStep(Step $step)
    {
        if (!$this->steps->contains($step)) {
            $this->steps->add($step);
        }

        return $this;
    }

    /**
     * Remove step.
     *
     * @param \Innova\PathBundle\Entity\Step $step
     *
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function removeStep(Step $step)
    {
        if ($this->steps->contains($step)) {
            $this->steps->removeElement($step);
        }

        return $this;
    }

    /**
     * Get steps.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set breadcrumbs.
     *
     * @param bool $breadcrumbs
     *
     * @return \Innova\PathBundle\Entity\Path\AbstractPath
     */
    public function setBreadcrumbs($breadcrumbs)
    {
        $this->breadcrumbs = $breadcrumbs;

        return $this;
    }

    /**
     * Does Path have a breadcrumbs ?
     *
     * @return bool
     */
    public function hasBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    /**
     * Get completeBlockingCondition.
     *
     * @return bool
     */
    public function isCompleteBlockingCondition()
    {
        return $this->completeBlockingCondition;
    }

    /**
     * Set completeBlockingCondition.
     *
     * @param bool completeBlockingCondition
     *
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function setCompleteBlockingCondition($completeBlockingCondition)
    {
        $this->completeBlockingCondition = $completeBlockingCondition;

        return $this;
    }

    /**
     * Get manualProgressionAllowed.
     *
     * @return bool
     */
    public function isManualProgressionAllowed()
    {
        return $this->manualProgressionAllowed;
    }

    /**
     * Set manualProgressionAllowed.
     *
     * @param bool manualProgressionAllowed
     *
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function setManualProgressionAllowed($manualProgressionAllowed)
    {
        $this->manualProgressionAllowed = $manualProgressionAllowed;

        return $this;
    }

    /**
     * Get root step of the path.
     *
     * @throws \Exception
     *
     * @return \Innova\PathBundle\Entity\Step
     */
    public function getRootStep()
    {
        $root = null;

        if ($this->isPublished() && !empty($this->steps)) {
            foreach ($this->steps as $step) {
                if (null === $step->getParent()) {
                    // Root step found
                    $root = $step;
                    break;
                }
            }
        }

        return $root;
    }

    /**
     * Initialize JSON structure.
     *
     * @return \Innova\PathBundle\Entity\Path\Path
     */
    public function initializeStructure()
    {
        $structure = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'breadcrumbs' => $this->breadcrumbs,
            'summaryDisplayed' => $this->summaryDisplayed,
            'completeBlockingCondition' => $this->completeBlockingCondition,
            'manualProgressionAllowed' => $this->manualProgressionAllowed,
            'steps' => [],
        ];

        $this->setStructure(json_encode($structure));

        return $this;
    }

    /**
     * Wrapper to access workspace of the Path.
     *
     * @return \Claroline\CoreBundle\Entity\Workspace\Workspace
     */
    public function getWorkspace()
    {
        $workspace = null;
        if (!empty($this->resourceNode)) {
            $workspace = $this->resourceNode->getWorkspace();
        }

        return $workspace;
    }

    /**
     * Wrapper to access creator of the Path.
     *
     * @return \Claroline\CoreBundle\Entity\User
     */
    public function getCreator()
    {
        $creator = null;
        if (!empty($this->resourceNode)) {
            $creator = $this->resourceNode->getCreator();
        }

        return $creator;
    }

    public function jsonSerialize()
    {
        $steps = [];

        $rootStep = $this->getRootStep();
        if (!empty($rootStep)) {
            $steps[] = $rootStep;
        }

        return [
            'id' => $this->id,
            'name' => $this->getResourceNode()->getName(),
            'description' => $this->description,
            'breadcrumbs' => $this->breadcrumbs,
            'summaryDisplayed' => $this->summaryDisplayed,
            'completeBlockingCondition' => $this->completeBlockingCondition,
            'manualProgressionAllowed' => $this->manualProgressionAllowed,
            'steps' => $steps,
        ];
    }
}
