package lab.server;

import com.sun.net.httpserver.HttpServer;
import com.sun.net.httpserver.HttpExchange;
import com.sun.net.httpserver.HttpHandler;
import java.io.*;
import java.net.InetSocketAddress;
import java.nio.file.Files;
import java.nio.file.Path;
public class StaticFileServer {
    private HttpServer server;
    private final int port;
    private final Path root;
    public StaticFileServer(int port, Path root) {
        this.port = port;
        this.root = root;
    }
    public void start() throws IOException {
        server = HttpServer.create(new InetSocketAddress(port), 0);
        server.createContext("/", new FileHandler(root));
        server.setExecutor(null);
        server.start();
        System.out.println("StaticFileServer started on http://localhost:" + port + " serving " +
                root);
    }
    public void stop() {
        if (server != null) {
            server.stop(0);
            System.out.println("StaticFileServer stopped");
        }
    }
    static class FileHandler implements HttpHandler {
        private final Path root;
        FileHandler(Path root) { this.root = root; }
        @Override
        public void handle(HttpExchange exchange) throws IOException {
            String path = exchange.getRequestURI().getPath();
            if (path.equals("/")) path = "/index.html";
            Path file = root.resolve(path.substring(1)).normalize();
            if (!file.startsWith(root) || !Files.exists(file) || Files.isDirectory(file)) {
                String resp = "404 (Not Found)\n";
                exchange.sendResponseHeaders(404, resp.length());
                try (OutputStream os = exchange.getResponseBody()) {
                    os.write(resp.getBytes());
                }
                return;
            }
            byte[] bytes = Files.readAllBytes(file);
            String mime = Files.probeContentType(file);
            if (mime == null) mime = "application/octet-stream";
            exchange.getResponseHeaders().add("Content-Type", mime);
            exchange.sendResponseHeaders(200, bytes.length);
            try (OutputStream os = exchange.getResponseBody()) {
                os.write(bytes);
            }
        }
    }
}
