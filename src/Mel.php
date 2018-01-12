<?php

namespace Mel;

class Mel
{
    /**
     * @var MeLiApp
     */
    protected $meLiApp;

    /**
     * Mel constructor.
     * @param MeLiApp $meLiApp
     */
    public function __construct(MeLiApp $meLiApp = null)
    {
        $this->meLiApp = $meLiApp ?: new MeLiApp(MeLiApp::ANONYMOUS_MODE);
    }

    /**
     * Return MeLiApp instance
     * @return MeLiApp
     */
    public function meLiApp()
    {
        return $this->meLiApp;
    }
}