package POMEjemplo.test.login;

import org.junit.jupiter.api.Test;

public class LoginWithPOMTest extends ParentTest {

    String username = "standard_user";
    String password = "secret_sauce";

    @Test
    public void validLogin() {
        loginPage.openLoginPage();
        loginPage.enterUserName(username);
        loginPage.enterPassWord(password);
        loginPage.clickOnLogin();

        productsPage.checkProductsPageOpened();
    }

}
