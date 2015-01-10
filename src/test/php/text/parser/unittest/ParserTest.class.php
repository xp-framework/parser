<?php namespace text\parser\unittest;

class ParserTest extends \unittest\TestCase {

  #[@test]
  public function parse_returns_result() {
    $parser= newinstance('text.parser.generic.AbstractParser', [], [
      'yyparse' => function($lexer) {
        $parsed= [];
        while ($lexer->advance()) {
          $parsed[]= $lexer->value;
        }
        return $parsed;
      },
      'yyname' => function($token) { },
      'yyexpecting' => function($state) { },
    ]);
    $this->assertEquals(['Hello'], $parser->parse(new TestLexer(['Hello'])));
  }

  #[@test, @expect('text.parser.generic.ParseException')]
  public function errors_cause_exception() {
    $parser= newinstance('text.parser.generic.AbstractParser', [], [
      'yyparse' => function($lexer) {
        $this->error(E_ERROR, 'Test');
        return ['Test'];
      },
      'yyname' => function($token) { },
      'yyexpecting' => function($state) { },
    ]);
    $parser->parse(new TestLexer([]));
  }

  #[@test, @expect('text.parser.generic.ParseException')]
  public function parse_cause_exception() {
    $parser= newinstance('text.parser.generic.AbstractParser', [], [
      'yyparse' => function($lexer) {
        $this->error(E_PARSE, 'Test');
        return ['Test'];
      },
      'yyname' => function($token) { },
      'yyexpecting' => function($state) { },
    ]);
    $parser->parse(new TestLexer([]));
  }

  #[@test]
  public function warnings_do_not_cause_exception() {
    $parser= newinstance('text.parser.generic.AbstractParser', [], [
      'yyparse' => function($lexer) {
        $this->error(E_WARNING, 'Test');
        return ['Test'];
      },
      'yyname' => function($token) { },
      'yyexpecting' => function($state) { },
    ]);
    $this->assertEquals(['Test'], $parser->parse(new TestLexer([])));
  }
}