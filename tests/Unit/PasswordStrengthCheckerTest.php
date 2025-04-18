<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\View\Components\PasswordStrengthChecker;
use App\Rules\CompromisedPassword;
use Illuminate\Support\Facades\Http;

class PasswordStrengthCheckerTest extends TestCase
{
    private PasswordStrengthChecker $checker;
    private CompromisedPassword $compromisedRule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->checker = new PasswordStrengthChecker();
        $this->compromisedRule = new CompromisedPassword();
    }

    /**
     * @dataProvider passwordRequirementsProvider
     */
    public function test_password_requirements(string $requirement, string $password, bool $shouldPass): void
    {
        $requirements = $this->checker->getRequirements();
        $pattern = $requirements[$requirement]['regex'];

        $result = preg_match($pattern, $password);

        $this->assertEquals($shouldPass, (bool)$result);
    }

    public function passwordRequirementsProvider(): array
    {
        return [
            'length_requirement_pass' => ['length', 'Password123!', true],
            'length_requirement_fail' => ['length', 'Pass1!', false],

            'uppercase_requirement_pass' => ['uppercase', 'Password123!', true],
            'uppercase_requirement_fail' => ['uppercase', 'password123!', false],

            'lowercase_requirement_pass' => ['lowercase', 'Password123!', true],
            'lowercase_requirement_fail' => ['lowercase', 'PASSWORD123!', false],

            'number_requirement_pass' => ['number', 'Password123!', true],
            'number_requirement_fail' => ['number', 'Password!!', false],

            'special_requirement_pass' => ['special', 'Password123!', true],
            'special_requirement_fail' => ['special', 'Password123', false],

            'compromised_requirement_pass' => ['compromised', 'StrongP@ss123!', true],
            'compromised_requirement_fail' => ['compromised', 'password123', false],
        ];
    }

    /**
     * Test complete password validation with all requirements
     */
    public function test_strong_password_passes_all_requirements(): void
    {
        $password = 'StrongP@ss123';
        $requirements = $this->checker->getRequirements();
        $allRequirementsMet = true;

        foreach ($requirements as $requirement) {
            if (!preg_match($requirement['regex'], $password)) {
                $allRequirementsMet = false;
                break;
            }
        }

        $this->assertTrue($allRequirementsMet);
    }

    /**
     * Test that common weak passwords fail
     */
    public function test_common_weak_passwords_fail(): void
    {
        $weakPasswords = [
            'password123',
            '12345678',
            'qwerty123',
            'letmein123'
        ];

        foreach ($weakPasswords as $password) {
            $this->assertFalse(
                preg_match($this->checker->getRequirements()['uppercase']['regex'], $password),
                "Password '$password' should fail uppercase requirement"
            );
        }
    }

    /**
     * Test compromised password validation
     */
    public function test_compromised_password_check(): void
    {
        // Mock the API response for a compromised password
        Http::fake([
            'api.pwnedpasswords.com/range/*' => Http::response(
                "0018A45C4D1DEF81644B54AB7F969B88D65:1\n" .
                    "00D4F6E8FA6EECAD2A3AA415EEC418D38EC:2\n",
                200
            )
        ]);

        $compromisedPassword = 'password123';
        $safePassword = 'StrongP@ss123!';

        // Test compromised password
        $this->assertFalse(
            $this->compromisedRule->passes('password', $compromisedPassword),
            "Common password '$compromisedPassword' should be detected as compromised"
        );

        // Test safe password
        $this->assertTrue(
            $this->compromisedRule->passes('password', $safePassword),
            "Strong password should not be marked as compromised"
        );
    }

    /**
     * Test API failure handling
     */
    public function test_compromised_check_handles_api_failure(): void
    {
        Http::fake([
            'api.pwnedpasswords.com/range/*' => Http::response(null, 500)
        ]);

        $password = 'TestP@ssword123';

        $this->assertTrue(
            $this->compromisedRule->passes('password', $password),
            'Validation should pass when API request fails'
        );
    }
}