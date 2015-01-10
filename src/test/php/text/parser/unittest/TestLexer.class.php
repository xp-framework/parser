<?php namespace text\parser\unittest;

class TestLexer extends \text\parser\generic\AbstractLexer {
  private $tokens, $number= 0;

  /**
   * Constructor
   *
   * @param  string[] $tokens
   */
  public function __construct($tokens) {
    $this->tokens= $tokens;
  }

  /**
   * Advance to next token. Return TRUE and set token, value and
   * position members to indicate we have more tokens, or FALSE
   * to indicate we've arrived at the end of the tokens.
   *
   * @return  bool
   */
  public function advance() {
    if (empty($this->tokens)) return false;

    $token= array_shift($this->tokens);
    $this->token= 0;
    $this->value= $token;
    $this->position= [1, $this->number++];
    return true;
  }
}