package ejemplosylabs.java.com.labs;

import org.apache.commons.compress.archivers.dump.InvalidFormatException;
import org.apache.poi.ss.usermodel.Sheet;
import org.apache.poi.ss.usermodel.Workbook;
import org.apache.poi.ss.usermodel.WorkbookFactory;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
public class DataExcel {
    public static Object[][] getExcelData(String filePath, String sheetName) throws
            IOException, InvalidFormatException {
        FileInputStream file = new FileInputStream(new File(filePath));
        Workbook workbook = WorkbookFactory.create(file);
        Sheet sheet = workbook.getSheet(sheetName);

        int rowCount = sheet.getPhysicalNumberOfRows();
        int colCount = sheet.getRow(0).getPhysicalNumberOfCells();

        Object[][] data = new Object[rowCount - 1][colCount]; // To ignore the header row

        for (int i = 1; i < rowCount; i++) { // Start from 1 to skip the header row
            for (int j = 0; j < colCount; j++) {
                data[i - 1][j] = sheet.getRow(i).getCell(j).toString();
            }
        }
        workbook.close();
        return data;
    }
}
