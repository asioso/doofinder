<?php
/**
 * This source file is available under  GNU General Public License version 3 (GPLv3)
 *
 * Full copyright and license information is available in LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Asioso GmbH (https://www.asioso.com)
 *
 */

namespace DooFinderBundle\RenderService;


use Pimcore\Model\Document\Page;
use Pimcore\Templating\Renderer\ActionRenderer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FrontendRenderService
 * @package DooFinderBundle\RenderService
 */
class FrontendRenderService
{
    /**
     * @var ActionRenderer
     */
    private $renderer;
    /**
     * @var RequestStack
     */
    private $stack;

    /**
     * FrontendRenderService constructor.
     * @param ActionRenderer $renderer
     * @param RequestStack $stack
     */
    public function __construct(ActionRenderer $renderer, RequestStack $stack)
    {
        $this->renderer = $renderer;
        $this->stack = $stack;
    }


    /**
     * @param Page $object
     * @return string
     */
    public function renderView(Page $object)
    {

        $request = Request::create(
            $object->getFullPath(),
            'GET',
            array('id' => $object->getId())
        );
        $this->stack->push($request);
        $html = $this->renderer->render($object);
        $content = $this->process($html);

        $this->stack->pop();
        return $content;
    }

    private function process(string $html)
    {
        # Create a DOM parser object
        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $content = "";
        $bodies = $dom->getElementsByTagName('body');
        //there should be only one
        foreach ($bodies as $body) {
            $this->processNode($body, $content);

        }
        return $content;
    }

    private function processNode(\DOMNode $node, &$content)
    {
        if ($node->hasChildNodes()) {
            foreach ($node->childNodes as $child) {
                $this->processNode($child, $content);
            }
        }

        $content = $content . $node->nodeValue;

    }


}