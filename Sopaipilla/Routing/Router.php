<?php

declare(strict_types=1);

namespace Sopaipilla\Routing;

use ReflectionClass;
use ReflectionMethod;

/**
 * Attribute-based HTTP router.
 *
 * Controllers register their routes by decorating public methods with
 * PHP 8 Attributes (#[Get], #[Post], etc.). The Router reflects over
 * each registered controller, collects those routes and dispatches
 * the incoming request to the first match.
 */
class Router
{
    /** All registered routes collected from controller attributes. */
    private array $routes = [];

    /**
     * Reflect over a controller instance and register every method
     * decorated with a routing attribute.
     */
    public function registerController(object $controller): void
    {
        $reflection = new ReflectionClass($controller);
        
        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            // Look for routing attributes
            $attributes = $method->getAttributes();
            
            foreach ($attributes as $attribute) {
                $attrName = $attribute->getName();
                
                // Check if it's a routing attribute
                if (str_ends_with($attrName, '\\Get') || 
                    str_ends_with($attrName, '\\Post') || 
                    str_ends_with($attrName, '\\Put') || 
                    str_ends_with($attrName, '\\Delete') || 
                    str_ends_with($attrName, '\\Patch')) {
                    
                    $route = $attribute->newInstance();
                    $httpMethod = strtoupper(basename(str_replace('\\', '/', $attrName)));
                    
                    $this->routes[] = [
                        'method' => $httpMethod,
                        'path' => $route->path,
                        'controller' => $controller,
                        'handler' => $method->getName(),
                    ];
                }
            }
        }
    }
    
    /**
     * Match the current request against the registered routes and call
     * the first matching handler. Responds with 404 if nothing matches.
     */
    public function dispatch(): void
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $requestPath = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
        
        foreach ($this->routes as $route) {
            $params = [];  // Initialize params array
            if ($route['method'] === $requestMethod && $this->matchRoute($route['path'], $requestPath, $params)) {
                echo call_user_func_array([$route['controller'], $route['handler']], $params);
                return;
            }
        }
        
        http_response_code(404);
        echo "404 - Not Found";
    }
    
    private function matchRoute(string $routePath, string $requestPath, array &$params): bool
    {
        // Normalize: strip leading/trailing slashes for comparison
        $routePath   = trim($routePath, '/');
        $requestPath = trim($requestPath, '/');

        // Exact match
        if ($routePath === $requestPath) {
            $params = [];
            return true;
        }

        // Pattern match with parameters
        $pattern = preg_quote($routePath, '#');
        $pattern = preg_replace('#\\\{([^}]+)\\\}#', '([^/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $requestPath, $matches)) {
            array_shift($matches);
            $params = $matches;
            return true;
        }

        return false;
    }
}