<?php
namespace PHPSass\Renderers;

/**
 * ExpandedRenderer class file.
 * @author      Chris Yates <chris.l.yates@gmail.com>
 * @copyright   Copyright (c) 2010 PBM Web Development
 * @license      http://phamlp.googlecode.com/files/license.txt
 */

/**
 * ExpandedRenderer class.
 * Expanded is the typical human-made CSS style, with each property and rule
 * taking up one line. Properties are indented within the rules, but the rules
 * are not indented in any special way.
 */
class ExpandedRenderer
extends CompactRenderer
{
	/**
	 * Renders the brace between the selectors and the properties
	 *
	 * @return string the brace between the selectors and the properties
	 */
	protected function between()
	{
		return " {\n";
	}

	/**
	 * Renders the brace at the end of the rule
	 *
	 * @return string the brace between the rule and its properties
	 */
	protected function end()
	{
		return "\n}\n\n";
	}

	/**
	 * Renders a comment.
	 *
	 * @param \PHPSass\Tree\Node the node being rendered
	 * @return string the rendered commnt
	 */
	public function renderComment($node)
	{
		if ($node->isInvisible(Renderer::STYLE_EXPANDED)) {
			return '';
			}
		$indent=$this->getIndent($node);
		$lines=array_map(function($line) {return trim($line);}, explode("\n", $node->value));

		return "$indent/*\n$indent * ".join("\n$indent * ", $lines)."\n$indent */".(empty($indent)? "\n" : '');
	}

	/**
	 * Renders properties.
	 *
	 * @param \PHPSass\Tree\Node $node
	 * @param array properties to render
	 * @return string the rendered properties
	 */
	public function renderProperties($node, $properties)
	{
		$indent=$this->getIndent($node).self::INDENT;

		return $indent.join("\n$indent", $properties);
	}
}
