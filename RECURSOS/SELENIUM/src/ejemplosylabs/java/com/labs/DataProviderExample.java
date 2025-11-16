package ejemplosylabs.java.com.labs;

import org.apache.commons.compress.archivers.dump.InvalidFormatException;
import org.testng.annotations.DataProvider;

import java.io.IOException;
public class DataProviderExample {
    @DataProvider(name = "loginData")
    public Object[][] provideLoginData() throws IOException, InvalidFormatException {
        // Provide the path to the Excel file and the sheet name
        return DataExcel.getExcelData("C:\\Users\\Jorge\\Downloads\\jorloicono AF-TESTING-AUTOMATIZADO-SELENIUM-CUCUMBER master RECURSOS\\SELENIUM\\src\\ejemplosylabs\\java\\com\\labs\\logindata.xlsx", "Sheet_Name which we give at the bottom in Excel File");
    }
}
