<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET");

\Magento\FunctionalTestingFramework\Config\MftfApplicationConfig::create(
    true,
    \Magento\FunctionalTestingFramework\Config\MftfApplicationConfig::GENERATION_PHASE,
    false
);


function returnTestCounts()
{
    ob_start(); // suppress any other output
    $tohInstance = \Magento\FunctionalTestingFramework\Test\Handlers\TestObjectHandler::getInstance();
    $allTests = $tohInstance->getAllObjects();

    $num_tests = count($allTests);
    $num_skipped_tests = 0;
    $test_actions = [];
    $test_action_names = [];
    $test_modules = [];

    /** @var \Magento\FunctionalTestingFramework\Test\Objects\TestObject $test */
    foreach ($allTests as $test) {
        if ($test->isSkipped()) {
            $num_skipped_tests++;
        }

        $test_actions[] = $test->getTestActionCount();
        $test_action_names = array_merge($test_action_names, returnTestActionNames($test->getOrderedActions()));
        $test_modules = appendTestModuleCount($test, $test_modules);
    }

    $response = [
        "number_of_tests" => $num_tests,
        "skipped_tests" => $num_skipped_tests,
        "test_action_counts" => array_count_values($test_action_names),
        "test_module_counts" => $test_modules,
        "avg_steps_per_test" => ceil(array_sum($test_actions)/$num_tests),
        "median_steps_per_test" => ceil(returnMedianTestSteps($test_actions))
    ];

    ob_end_clean();
    return json_encode($response, JSON_PRETTY_PRINT);
}

function returnTestActionNames($testActions)
{
    $types = [];
    /** @var \Magento\FunctionalTestingFramework\Test\Objects\ActionObject $testAction */
    foreach ($testActions as $testAction) {
        $types[] = $testAction->getType();
    }

    return $types;
}

/**
 * Add the test to the test module array
 *
 * @param \Magento\FunctionalTestingFramework\Test\Objects\TestObject $test
 */
function appendTestModuleCount($test, $test_modules)
{
    $module = $test->getAnnotations()['features'][0];

    if (!array_key_exists($module, $test_modules)) {
        $test_modules[$module] = 0;
    }

    $test_modules[$module] = $test_modules[$module] + 1;
    return $test_modules;
}

function returnMedianTestSteps($testStepArray)
{
    arsort($testStepArray);
    if (count($testStepArray) % 2 == 0) {
         $middleValue = count($testStepArray) / 2;
         return $testStepArray[$middleValue];
    }

    $middleValue = (count($testStepArray) - 1) / 2;
    $middleValue1 =  $middleValue + 1;

    return ($testStepArray[$middleValue] + $testStepArray[$middleValue1]) / 2;
}
