<?php
declare(strict_types=1);
namespace TYPO3\CMS\Grid\Form\Node;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Form\NodeFactory;

/**
 * Abstract container has various methods used by the container classes
 */
abstract class AbstractContainer extends AbstractNode
{
    /**
     * Instance of the node factory to create sub elements and container.
     *
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * Container objects give $nodeFactory down to other containers.
     *
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);
        $this->nodeFactory = $nodeFactory;
    }
}
