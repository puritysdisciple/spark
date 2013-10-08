<?php

require_once(__DIR__ . '/../vendor/autoload.php');

use Michelf\Markdown;

class Twig_Node_Markdown extends Twig_Node
{
    public function __construct(Twig_NodeInterface $body, $lineno, $tag = 'markdown')
    {
        parent::__construct(array('body' => $body), array(), $lineno, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $content = Markdown::defaultTransform($this->getNode('body')->getAttribute('data'));

        $compiler
            ->write('echo ')
            ->string($content)
            ->raw(";\n");
    }
}

class Markdown_Set_TokenParser extends Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();

        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideMarkdownEnd'), true);
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);

        return new Twig_Node_Markdown($body, $lineno, $this->getTag());
    }

    public function decideMarkdownEnd(Twig_Token $token)
    {
        return $token->test('endmarkdown');
    }

    public function getTag()
    {
        return 'markdown';
    }
}

class Markdown_Extension extends Twig_Extension
{
    public function getName()
    {
        return 'markdown';
    }

    public function getTokenParsers()
    {
        return array(new Markdown_Set_TokenParser());
    }
}

$spark->addTwigExtension(new Markdown_Extension());
