package testrunner;

import io.cucumber.testng.AbstractTestNGCucumberTests;
import io.cucumber.testng.CucumberOptions;

@CucumberOptions(
        features = "C:\\Users\\Jorge\\Desktop\\AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER\\RECURSOS\\CUCUMBER\\hellocucumber\\src\\test\\resources\\features", // Correct path to the feature file
        glue = "stepdefinitions", // Path to step definitions
        plugin = {"pretty", "html:target/cucumber-reports"} // Generate HTML report
)
public class TestRunner extends AbstractTestNGCucumberTests {
}