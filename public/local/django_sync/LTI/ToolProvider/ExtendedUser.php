<?php

namespace app\local\django_sync\LTI\ToolProvider;
use IMSGlobal\LTI\ToolProvider\User;

class ExtendedUser extends User
{
    /**
     * User's edeboid.
     *
     * @var string
     */
    private $edeboid = '';

    /**
     * User's iasid.
     *
     * @var string
     */
    private $iasid = '';

    /**
     * User's djangoid.
     *
     * @var string
     */
    private $djangoid = '';

    /**
     * Class constructor.
     */
    public function __construct()
    {
        // Call parent constructor if necessary
        parent::__construct();

        // Initialize custom fields
        $this->initialize();
    }

    /**
     * Initialise custom fields.
     */
    public function initialize()
    {
        $this->edeboid = '';
        $this->iasid = '';
        $this->djangoid = '';
    }

    /**
     * Get the edeboid.
     *
     * @return string
     */
    public function getEdeboid(): string
    {
        return $this->edeboid;
    }

    /**
     * Set the edeboid.
     *
     * @param string $edeboid
     */
    public function setEdeboid(string $edeboid): void
    {
        $this->edeboid = $edeboid;
    }

    /**
     * Get the iasid.
     *
     * @return string
     */
    public function getIasid(): string
    {
        return $this->iasid;
    }

    /**
     * Set the iasid.
     *
     * @param string $iasid
     */
    public function setIasid(string $iasid): void
    {
        $this->iasid = $iasid;
    }

    /**
     * Get the djangoid.
     *
     * @return string
     */
    public function getDjangoid(): string
    {
        return $this->djangoid;
    }

    /**
     * Set the djangoid.
     *
     * @param string $djangoid
     */
    public function setDjangoid(string $djangoid): void
    {
        $this->djangoid = $djangoid;
    }
}
