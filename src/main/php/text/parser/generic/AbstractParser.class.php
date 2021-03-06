<?php namespace text\parser\generic;

use util\log\Traceable;
use lang\Throwable;
use lang\FormatException;

/**
 * Abstract parser. Subclasses of this class are generated!
 *
 * @test   xp://text.parser.unittest.ParserTest
 */
abstract class AbstractParser extends \lang\Object {
  const ERROR   = 0x0000;
  const WARNING = 0x0001;

  protected
    $cat          = null,
    $levels       = array(
      E_ERROR       => self::ERROR,
      E_PARSE       => self::ERROR,
      E_WARNING     => self::WARNING,
    ),
    $messages     = [];
  
  /**
   * Returns whether errors have occured
   *
   * @return  bool
   */
  public function hasErrors() {
    return !empty($this->messages[self::ERROR]);
  }

  /**
   * Returns errors
   *
   * @return  text.parser.generic.ParserMessage[]
   */
  public function getErrors() {
    return $this->messages[self::ERROR];
  }

  /**
   * Returns whether warnings have occured
   *
   * @return  bool
   */
  public function hasWarnings() {
    return !empty($this->messages[self::WARNING]);
  }

  /**
   * Returns warnings
   *
   * @return  text.parser.generic.ParserMessage[]
   */
  public function getWarnings() {
    return $this->messages[self::WARNING];
  }

  /**
   * Map a level to a type
   *
   * @param   int level
   * @param   int type one of ERROR or WARNING constants
   */
  public function mapLevel($level, $type) {
    $this->levels[$level]= $type;
  }

  /**
   * Error handler
   *
   * @param   int level
   * @param   string message
   * @param   string[] expected
   */
  public function error($level, $message, $expected= []) {
    $m= new ParserMessage($level, $message, $expected);

    $this->messages[$this->levels[$level]][]= $m;
    $this->cat && $this->cat->info($m);

    return false;
  }

  /**
   * Set a logger category for debugging
   *
   * @param   util.log.LogCategory cat
   */
  public function setTrace($cat) {
    $this->cat= $cat;
  }
  
  /**
   * Parse
   *
   * @param   text.parser.generic.AbstractLexer lexer
   * @return  var result of the last reduction, if any.
   * @throws  text.parser.generic.ParseException if an exception occurs during parsing.
   */
  public function parse(AbstractLexer $lexer) {
    $this->messages= array(
      self::ERROR   => [],
      self::WARNING => [],
    );

    try {
      $result= $this->yyparse($lexer);
    } catch (Throwable $e) {
      throw new ParseException($e->getMessage(), $e);
    }
    
    if (!empty($this->messages[self::ERROR])) {
      $p= new ParseException(sizeof($this->messages[self::ERROR]).' parse error(s) occurred.', null, $this->messages[self::ERROR]);
      $p->setCause(new FormatException("[\n".$p->formattedErrors().']'));
      throw $p;
    }
    
    return $result;
  }
  
  /**
   * Parser main method. Maintains a state and a value stack, 
   * currently with fixed maximum size.
   *
   * @param   text.parser.generic.AbstractLexer lexer
   * @return  var result of the last reduction, if any.
   */
  public abstract function yyparse($lexer);
  
  /**
   * Retrieves name of a given token
   *
   * @param   int token
   * @return  string name
   */
  protected abstract function yyname($token);
  
  /**
   * Computes list of expected tokens on error by tracing the tables.
   *
   * @param   int state for which to compute the list.
   * @return  string[] list of token names.
   */
  protected abstract function yyexpecting($state);

}
