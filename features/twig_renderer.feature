  Feature: Twig Renderer

  Background:
    Given a file named "behat.yml" with:
      """
      default:
        formatters:
            html:
                output_path: %paths.base%/build
        extensions:
            emuse\BehatHTMLFormatter\BehatHTMLFormatterExtension:
                name: html
                renderer: Twig
                file_name: Index
                print_args: true
                print_outp: true
                loop_break: true
        suites:
            suite1:
                paths:    [ "%paths.base%/features/suite1" ]
            suite2:
                paths:    [ "%paths.base%/features/suite2" ]
            suite3:
                paths:    [ "%paths.base%/features/suite3" ]
      """
    Given a file named "features/bootstrap/FeatureContext.php" with:
      """
      <?php
        use Behat\Behat\Context\CustomSnippetAcceptingContext,
            Behat\Behat\Tester\Exception\PendingException;
        class FeatureContext implements CustomSnippetAcceptingContext
        {
            public static function getAcceptedSnippetType() { return 'regex'; }
            /** @When /^I give a passing step$/ */
            public function passingStep() { 
              PHPUnit_Framework_Assert::assertEquals(2, 2);
            }
            /** @When /^I give a failing step$/ */
            public function failingStep() { 
              PHPUnit_Framework_Assert::assertEquals(1, 2);
            }
            /** * @When /^I give a pending step$/ */
            public function somethingNotDoneYet() {
                throw new PendingException();
            }
        }
      """

 Scenario: Multiple Suites with multiple results
    Given a file named "features/suite1/suite_failing_with_passing.feature" with:
      """
      Feature: Suite failing with passing scenarios
        Scenario: Passing scenario
          Then I give a passing step
        Scenario: One Failing step
          Then I give a failing step
        Scenario: One Pending step
          Then I give a pending step
        Scenario: Passing and Pending steps
          Then I give a passing step
          Then I give a pending step
        Scenario: Passing and Failing steps
          Then I give a passing step
          Then I give a failing step
      """
    Given a file named "features/suite2/suite_passing.feature" with:
      """
      Feature: Suite passing
        Scenario: Passing scenario
          Then I give a passing step
      """
    Given a file named "features/suite3/suite_pending.feature" with:
      """
      Feature: Suite with pending scenario
        Scenario: One pending step
          Then I give a pending step
      """
    When I run "behat --no-colors"
    Then process output should be:
      """

      --- FeatureContext has missing steps. Define them with these snippets:

          /**
           * @Then /^I give a pending step$/
           */
          public function iGiveAPendingStep()
          {
              throw new PendingException();
          }


      """
    And report file should exists
    And report file should contain:
      """
      2 features failed of 3
      """
    And report file should contain:
      """
      5 scenarios failed of 7
      """
    And report file should contain:
      """
      2 steps failed of 6
      """

