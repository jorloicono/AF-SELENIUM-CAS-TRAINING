package ejemplosylabs.java.com.ejemplos;

import org.testng.annotations.DataProvider;

public class Clase19_DataProvider {

    @DataProvider(name = "loginData")
    public Object[][] provideLoginData() {
        return new Object[][] {
                { "standard_user", "secret_sauce" },
                { "locked_out_user", "secret_sauce" },
                { "problem_user", "secret_sauce" }
        };
    }
}