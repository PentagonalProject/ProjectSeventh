<?php
namespace PentagonalProject\ProjectSeventh\Utilities;

/**
 * Class PasswordCheck
 * @package PentagonalProject\ProjectSeventh\Utilities
 */
class PasswordCheck
{
    const PASSWORD_EMPTY    = 0;
    const PASSWORD_WEAK     = 1;
    const PASSWORD_MODERATE = 2;
    const PASSWORD_GOOD     = 3;
    const PASSWORD_STRONG   = 4;

    /**
     * Regex Strong Password
     * @uses $minimumLength for replace of:
     *      :min_length:
     */
    const REGEX_STRONG_PASS_TEST = '/
        ^(?:
            (?=.*[0-9])
            (?=.*[a-z])
            (?=.*[A-Z])
            (?=.*[\~\\\.\+\*\?\^\$\[\]\(\)\|\{\}\\\'\#\_\-\&\%\@\=\"\!\<\>\`\;\:\s])
            (
                (\S|\s|\d){:min_length:,}
            )
            | (?=.*[0-9])
            (?=.*[a-z])
            (?=.*[A-Z])
            (?=.*[\~\\\.\+\*\?\^\$\[\]\(\)\|\{\}\\\'\#\_\-\&\%\@\=\"\!\<\>\`\;\:])
            (
                (\w|\s|\S|\d){:min_length:,}
            )
        )$
    /mx';

    /**
     * Regex for Good Password
     */
    const REGEX_GOOD_PASS_TEST = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])((.+){7,})$/im';

    /**
     * Regex For Moderate Password
     */
    const REGEX_MODERATE_PASS_TEST = '/^(?=.*[a-z])(?=.*[0-9])((\S|\s){6,})$/im';

    /**
     * Regex for Weak Password
     */
    const REGEX_WEAK_PASS_TEST = '/^(?=.*([a-z]|[0-9]))((\S|\s){1,})$/im';

    /**
     * Minimum Password Length
     *
     * @var int
     */
    protected $minimumLength = 8;

    /**
     * @var string
     */
    protected $storedPassword;

    /**
     * Result Status
     *
     * @var int
     */
    protected $status;

    /**
     * @var bool[]
     */
    protected $statusProcess = [];

    /**
     * PasswordCheck constructor.
     * @param string $password
     */
    public function __construct(string $password)
    {
        $this->storedPassword = $password;
    }

    /**
     * Get Password Regex
     *
     * @return string
     */
    public function getPasswordRegex() : string
    {
        return str_replace(
            ':min_length:',
            $this->minimumLength,
            self::REGEX_STRONG_PASS_TEST
        );
    }


    /**
     * Check if Password is Strong
     *
     * @param string $pass
     * @return bool
     */
    protected function testForStrongPass(string $pass) : bool
    {
        if (! isset($this->statusProcess[self::PASSWORD_STRONG])) {
            $this->statusProcess[self::PASSWORD_STRONG] = (bool) (
                    $this->testForGoodness($pass)
                    && preg_match($this->getPasswordRegex(), $pass)
                );
        }

        return $this->statusProcess[self::PASSWORD_STRONG];
    }

    /**
     * Check if Password Is Weak
     *
     * @param string $pass
     * @return bool
     */
    protected function testForWeakness(string $pass) : bool
    {
        if (! isset($this->statusProcess[self::PASSWORD_WEAK])) {
            $this->statusProcess[self::PASSWORD_WEAK] = (bool) (
                preg_match(self::REGEX_WEAK_PASS_TEST, $pass)
            );
        }

        return $this->statusProcess[self::PASSWORD_WEAK];
    }

    /**
     * check if Password is Good
     *
     * @param string $pass
     * @return bool
     */
    protected function testForGoodness(string $pass) : bool
    {
        if (! isset($this->statusProcess[self::PASSWORD_GOOD])) {
            $this->statusProcess[self::PASSWORD_GOOD] = (bool)(
                $this->testForModerate($pass) && preg_match(self::REGEX_GOOD_PASS_TEST, $pass)
            );
        }

        return $this->statusProcess[self::PASSWORD_GOOD];
    }

    /**
     * check if Password pass Moderate
     *
     * @param string $pass
     * @return bool
     */
    protected function testForModerate(string $pass) : bool
    {
        if (! isset($this->statusProcess[self::PASSWORD_MODERATE])) {
            $this->statusProcess[self::PASSWORD_MODERATE] = (bool) (
                $this->testForWeakness($pass)
                && preg_match(self::REGEX_GOOD_PASS_TEST, $pass)
            );
        }

        return $this->statusProcess[self::PASSWORD_MODERATE];
    }

    /**
     * check if Password is empty
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        return $this->getScore() < self::PASSWORD_WEAK;
    }

    /**
     * check if Password is weak
     *
     * @return bool
     */
    public function isWeak() : bool
    {
        return $this->getScore() === self::PASSWORD_WEAK;
    }

    /**
     * check if Password is moderate
     *
     * @return bool
     */
    public function isModerate() : bool
    {
        return $this->getScore() === self::PASSWORD_MODERATE;
    }

    /**
     * check if Password is good
     *
     * @return bool
     */
    public function isGood() : bool
    {
        return $this->getScore() === self::PASSWORD_GOOD;
    }

    /**
     * check if Password is good
     *
     * @return bool
     */
    public function isStrong() : bool
    {
        return $this->getScore() > self::PASSWORD_GOOD;
    }

    /**
     * Returning Score By Password with return value Constant
     *
     * @return int
     */
    public function getScore() : int
    {
        if (isset($this->status)) {
            return $this->status;
        }

        $storedPassword = trim($this->storedPassword);
        if ($storedPassword == '') {
            return $this->status = self::PASSWORD_EMPTY;
        }

        // always minimum length is 8
        $this->minimumLength < 8 && $this->minimumLength = 8;

        if ($this->testForStrongPass($storedPassword)) {
            return $this->status = self::PASSWORD_STRONG;
        }

        if ($this->testForGoodness($storedPassword)) {
            return $this->status = self::PASSWORD_GOOD;
        }

        if ($this->testForModerate($storedPassword)) {
            return $this->status = self::PASSWORD_MODERATE;
        }

        if ($this->testForModerate($storedPassword)) {
            return $this->status = self::PASSWORD_MODERATE;
        }

        return $this->status = self::PASSWORD_WEAK;
    }
}
