<?php
namespace Ks\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sessions")
 * @ORM\Entity(repositoryClass="Ks\CoreBundle\Entity\SessionRepository")
 */
class Session
{
	/**
     * @ORM\Id
     * @ORM\Column(type="string", length=255)
     */
    private $sess_id;
	
	/**
     * @ORM\Column(type="blob")
	 */
    private $sess_data;
	
	/**
     * @ORM\Column(type="integer")
	 */
    private $sess_time;
	
	/**
     * @ORM\Column(type="integer")
	 */
    private $sess_lifetime;
	
	/**
     * Constructor
     */
    public function __construct()
    {
    }
}
