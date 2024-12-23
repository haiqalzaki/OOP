<?php

require_once "utils.php";

class Items
{
    private array $name = [];
    private array $quantity = [];
    private array $price = [];
    protected array $items = [];

    // Pass user input to instantiate item object for processing / Can pass arrays separately, ie. can only pass the item name only, or quantity only, or price only if required
    public function __construct(array $itemName = [], array $itemQuantity = [], array $itemPrice = []) 
    {
        $this->name = $itemName;
        $this->quantity = $itemQuantity;
        $this->price = $itemPrice;
    }

    // Get validated name elements from object and return to caller (EXTERNAL)
    public function get_name_elements(): array|null
    {
        if ($this->validate_name() === false) {
            return null;
        }

        if (empty($this->name) || !is_array($this->name)) {
            return null;
        }

        return $this->name;
    }

    // Get validated quantity elements from object and return to caller (EXTERNAL)
    public function get_quantity_elements(): array|null
    {
        if ($this->validate_quantity() === false) {
            return null;
        }

        if (empty($this->quantity) || !is_array($this->quantity)) {
            return null;
        }

        return $this->quantity;
    }

    // Get validated price elements from object and return to caller (EXTERNAL)
    public function get_price_elements(): array|null
    {
        if ($this->validate_price() === false) {
            return null;
        }

        if (empty($this->price) || !is_array($this->price)) {
            return null;
        }

        return $this->price;
    }

    // Validate all array params (EXTERNAL)
    public function validate_items_and_set(): array
    {
        if (empty($this->name) || empty($this->quantity) || empty($this->price)) {
            return sendResult(false,'Item\'s array is empty!');
        }
        if (!$this->check_array_equal($this->name, $this->quantity, $this->price)) {
            return sendResult(false,'Item\'s array is not the same length.');
        }
        if (!$this->validate_name()) {
            return sendResult(false,'Item\'s name is not a valid input.');
        }
        if (!$this->validate_quantity()) {
            return sendResult(false,'Item\'s quantity is not a valid input');
        }
        if (!$this->validate_price()) {
            return sendResult(false,'Item\'s price is not a valid input');
        }

        $this->set_items();

        return sendResult(true, "Validation successful!");
    }

    // Get all validated elements from object and return to caller (EXTERNAL)
    public function get_items(): array|null 
    {
        return $this->items;
    }

    public function get_total_price(): float|null
    {
        if ((empty($this->quantity) || !is_array($this->quantity)) || (empty($this->price) || !is_array($this->price))) {
            return null;
        }

        $total = 0.0;

        foreach ($this->quantity as $index => $qty_value) {
            $price_value = (float) $this->price[$index];
            $total += $price_value * $qty_value;
        }

        return $total;
    }

    // Set all elements to item array property (INTERNAL)
    private function set_items(): void
    {
        $this->items['product_name'] = $this->name ?? [];
        $this->items['product_qty'] = $this->quantity ?? [];
        $this->items['product_price'] = $this->price ?? [];
    } 

    // Check array lengths (INTERNAL)
    private function check_array_equal(array $name, array $qty, array $price): bool
    {
        if (!(is_array($name) && is_array($qty) && is_array($price))) {
            error_log("Invalid input: One or more arguments are not arrays. ".date("h:i:sa")." \n\r", 3, 'item_error.txt');
            return false;
        }
        if (count($name) !== count($qty) || count($qty) !== count($price)) {
            error_log("Invalid input: One or more array didn't have the same length. ".date("h:i:sa")." \n\r", 3, 'item_error.txt');
            return false;
        }
        return true;
    }

    // Validator mutate object properties (INTERNAL)
    private function validate_name(): bool
    {
        foreach ($this->name as &$value) 
        {
            if (empty($value)) 
            {
                error_log("Invalid input: One or more arguments is empty. ".date("h:i:sa")." \n\r", 3, 'item_error.txt');
                return false;
            }
            if (!(is_string($value))) 
            {
                error_log("Invalid input: One or more arguments is not a string. ".date("h:i:sa")." \n\r", 3, 'item_error.txt');
                return false;
            }

            $value = htmlspecialchars(ucwords(trim($value)));
        }
        return true;
    }

    // Validator mutate object properties (INTERNAL)
    private function validate_quantity(): bool
    {
        foreach ($this->quantity as &$value) 
        {
            if (empty($value) || !is_numeric($value)) 
            {
                error_log("Invalid input: One or more arguments for quantity field is not numeric or empty. ".date("h:i:sa")." \n\r", 3, 'item_error.txt');
                return false;
            }

            if (is_string(trim($value)))
            {
                $value = (int) $value;
            }
        }
        return true;
    }

    // Validator mutate object properties (INTERNAL)
    private function validate_price(): bool
    {
        foreach ($this->price as &$value) 
        {
            if (empty($value) || !is_numeric($value)) 
            {
                error_log("Invalid input: One or more arguments for price fields is not numeric or empty. ".date("h:i:sa")." \n\r", 3, 'item_error.txt');
                return false;
            }

            if (is_string(trim($value)))
            {
                $value = (float) $value;
            }
        }
        return true;
    }  
}

$arr_name = ['brake pad', 'chain lube', 'oil filter'];
$arr_quantity = [5, 5, 5];
$arr_price = [19.99, 15.99, 10.99];

try {
    $item_one = new Items($arr_name, $arr_quantity, $arr_price);
    $result = $item_one->validate_items_and_set();
    
    if (!$result['status']) {
        echo "Operation failed: ". $result['message'];
        exit();
    }

    $items = $item_one->get_items();
    $total = $item_one->get_total_price();
    dd($total);
} catch (Exception $e) {
    error_log("Exception caught: ". $e->getMessage());
}

