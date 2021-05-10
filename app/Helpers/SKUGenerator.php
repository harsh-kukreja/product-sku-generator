<?php


namespace App\Helpers;


class SKUGenerator {

    public function formatVariantValue(array $variants) {
        $outer_array = array();
        $unique_array = array();
        foreach($variants as $key => $value) {

            $inner_array = array();

            $name = $value['name'];
            if(!in_array($value['name'], $unique_array)) {
                array_push($unique_array, $name);
                unset($value['name']);
                array_push($inner_array, array($value['value'], $value['variant_value_id']));
                $outer_array[$name] = $inner_array;
            } else {
                unset($value['name']);
                array_push($outer_array[$name], array($value['value'], $value['variant_value_id']));

            }
        }
        return $outer_array;
    }

    public function generate($productId, array $variants) {
        $formattedVariant = $this->formatVariantValue($variants);
        $cartisanProduct = $this->generateCartisanProduct($formattedVariant);
        return $this->generateSKU($productId, $cartisanProduct);
    }

    public function generateCartisanProduct(array $variants) {
        $input  = array_filter($variants);
        $result = array(array());
        foreach ($input as $key => $values) {
            $temp = array();
            foreach($result as $product) {
                foreach($values as $item) {
                    $product[$key] = $item;
                    $temp[] = $product;
                }
            }
            $result = $temp;
        }
        return $result;
    }

    public function generateSKU($productId, array $variants) {
        $result = array();
        foreach ($variants as $variant) {
            $ids = array();
            foreach ($variant as $vals) {
                $ids[] = $vals[0];
            }
            $result[$productId . '-' . implode('-', $ids)] = $variant;
        }
        return $result;
    }
}
