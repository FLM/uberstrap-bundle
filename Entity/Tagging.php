<?php

namespace FLM\UberstrapBundle\Entity;

use Anh\Taggable\Entity\MappedSuperclass\AbstractTagging;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="tagging_idx", columns={"tag_id", "resource_type", "resource_id"})
 *      }
 * )
 * @ORM\Entity(repositoryClass="Anh\Taggable\Entity\TaggingRepository")
 */
class Tagging extends AbstractTagging
{
    /**
     * @var $tag
     *
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="tagging", fetch="EAGER")
     */
    protected $tag;
}
