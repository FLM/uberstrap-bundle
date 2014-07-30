<?php

namespace FLM\UberstrapBundle\Entity;

use Anh\Taggable\Entity\MappedSuperclass\AbstractTag;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="name_idx", columns={"name"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Anh\Taggable\Entity\TagRepository")
 */
class Tag extends AbstractTag
{
    /**
     * @var $tagging
     *
     * @ORM\OneToMany(targetEntity="Tagging", mappedBy="tag", cascade={"remove"})
     */
    protected $tagging;
}
