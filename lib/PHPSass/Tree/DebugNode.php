<?php
namespace PHPSass\Tree;

/**
 * DebugNode class file.
 * @author      Chris Yates <chris.l.yates@gmail.com>
 * @copyright   Copyright (c) 2010 PBM Web Development
 * @license      http://phamlp.googlecode.com/files/license.txt
 */

/**
 * DebugNode class.
 * Represents a Sass @debug or @warn directive.
 */
class DebugNode
extends Node
{
	const IDENTIFIER='@';
	const MATCH='/^@(?:debug|warn)\s+(.+?)\s*;?$/';
	const MESSAGE=1;

	/** @var string the debug/warning message */
	private $message;
	/**
	 * @var array parameters for the message;
	 * only used by internal warning messages
	 */
	private $params;
	/** @var bool TRUE if this is a warning */
	private $warning;


	/**
	 * @param object $token source token
	 * @param mixed $message string: an internally generated warning message about the source
	 * bool: the source token is a @debug or @warn directive containing the message; TRUE if this is a @warn directive
	 */
	public function __construct($token, $message=FALSE)
	{
		parent::__construct($token);
		if (is_string($message)) {
			$this->message=$message;
			$this->warning=TRUE;
			}
		else {
			preg_match(self::MATCH, $token->source, $matches);
			$this->message=$matches[self::MESSAGE];
			$this->warning=$message;
			}
	}

	/**
	 * Parse this node.
	 * This raises an error.
	 *
	 * @param Context $context
	 * @return array An empty array
	 */
	public function parse($context)
	{
		if (!$this->warning) {
			$result=$this->evaluate($this->message, $context)->toString();

			if ($cb=\PHPSass\Parser::$instance->options['callbacks']['debug']) {
				call_user_func($cb, $result, $context);
				}
			else {
				set_error_handler([$this, 'errorHandler']);
				trigger_error($result);
				restore_error_handler();
				}
			}

		return [];
	}

	/**
	 * Error handler for degug and warning statements.
	 *
	 * @param int $errno Error number
	 * @param string $message Message
	 */
	public function errorHandler($errno, $message)
	{
		echo '<div style="background-color:#ce4dd6;border-bottom:1px dashed #88338d;color:white;font:10pt verdana;margin:0;padding:0.5em 2%;width:96%;"><p style="height:auto;margin:0.25em 0;padding:0;width:100%;"><span style="font-weight:bold;">SASS '.($this->warning ? 'WARNING' : 'DEBUG').":</span> $message</p><p style=\"margin:0.1em;padding:0;padding-left:0.5em;width:100%;\">{$this->filename}::{$this->line}</p><p style=\"margin:0.1em;padding:0;padding-left:0.5em;width:100%;\">Source: {$this->source}</p></div>";
	}
}
