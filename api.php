<?php
// api.php (Controller - handles requests and calls the model)
header("Content-Type: application/json"); // Set response type to JSON

require_once('product.php'); // Include the product model

// Determine the action based on the request.
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

switch ($action) {
    case 'getProducts':
        $products = getProducts();
        if ($products === false) {
            // Logged in model
            http_response_code(500);
            echo json_encode(array("error" => "Failed to retrieve products"));
        } else {
            echo json_encode($products);
        }
        break;

    case 'getProductById':
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            http_response_code(400); // Bad Request
            echo json_encode(array("error" => "Invalid product ID"));
            break;
        }
        $productId = intval($_GET['id']); // Sanitize
        $product = getProductById($productId);
        if ($product === false) {
             // Logged in model
            http_response_code(500);
            echo json_encode(array("error" => "Failed to retrieve product"));
        } else if (!$product) {
            http_response_code(404); // Not Found
            echo json_encode(array("error" => "Product not found"));
        } else {
            echo json_encode($product);
        }
        break;

    case 'createProduct':
       // print_r($_POST);  //for debugging
        if (
            !isset($_POST['name']) || empty($_POST['name']) ||
            !isset($_POST['listPrice']) || !is_numeric($_POST['listPrice'])
        ) {
            http_response_code(400); // Bad Request
            echo json_encode(array("error" => "Name and ListPrice are required and ListPrice must be numeric."));
            break;
        }

        $productData = array(
            'name' => $_POST['name'],  // already isset
            'productNumber' => $_POST['productNumber'] ?? null, //use null coalescing
            'listPrice' => floatval($_POST['listPrice']), //sanitize
            'sellStartDate' => $_POST['sellStartDate'] ?? date('Y-m-d H:i:s'),
            'standardCost' => $_POST['standardCost'] ?? 0
        );

        $newProductId = createProduct($productData);
        if ($newProductId === false) {
            // Error already logged in createProduct
            http_response_code(500);
            echo json_encode(array("error" => "Failed to create product"));
        } else {
            http_response_code(201); // Created
            echo json_encode(array("message" => "Product created successfully", "productId" => $newProductId));
        }
        break;

    default:
        http_response_code(400); // Bad Request
        echo json_encode(array("error" => "Invalid action"));
}
?>
