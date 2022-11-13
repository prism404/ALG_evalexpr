<?php

function calculate($value1, $value2, $operator)
{
    switch ($operator) {
        case "*":
            return $value1 * $value2;
        case "/":
            return $value1 / $value2;
        case "%":
            return $value1 % $value2;
        case "+":
            return $value1 + $value2;
        case "-":
            return $value1 - $value2;
    }
}

// Parse and calculate 
function calculateExpression($currentExpression)
{
    $numbers = "";
    $numbersArray = [];
    $operatorArray = [];

    // we divide the numbers to an array and the operators to another one
    $calculateAtNextNumber = false;
    for ($i = 0; $i < strlen($currentExpression); ++$i) {
        if (
            $currentExpression[$i] == "+" ||
            $currentExpression[$i] == "-" ||
            $currentExpression[$i] == "*" ||
            $currentExpression[$i] == "/" ||
            $currentExpression[$i] == "%"
        ) {
            array_push($numbersArray, $numbers);
            $numbers = ""; // empty after the push so we can add another value
            if ($calculateAtNextNumber) {
                $operator = array_pop($operatorArray); // delete and copy the last value of the array
                $value2 = floatval(array_pop($numbersArray));
                $value1 = floatval(array_pop($numbersArray));
                $calculateResult = calculate($value1, $value2, $operator);
                array_push($numbersArray, $calculateResult);
                $calculateAtNextNumber = false;
            }
            if (
                $currentExpression[$i] == "*" ||
                $currentExpression[$i] == "/" ||
                $currentExpression[$i] == "%"
            ) {
                // Order of priority for multiply, divide and modulo
                $calculateAtNextNumber = true;
            }
            array_push($operatorArray, $currentExpression[$i]);
        } else {
            $numbers = $numbers . $currentExpression[$i];
        }
    }

    // end of the expression
    if (strlen($numbers) != 0) {
        array_push($numbersArray, $numbers);
        if ($calculateAtNextNumber) {
            $operator = array_pop($operatorArray);
            $value2 = floatval(array_pop($numbersArray));
            $value1 = floatval(array_pop($numbersArray));
            $calculateResult = calculate($value1, $value2, $operator);
            array_push($numbersArray, $calculateResult);
            $calculateAtNextNumber = false;
        }
        $numbers = "";
    }

    // add calculate and substract the remaining
    for ($i = count($operatorArray) - 1; $i >= 0; $i--) {
        $operator = $operatorArray[$i];
        $value2 = floatval(array_pop($numbersArray));
        $value1 = floatval(array_pop($numbersArray));
        $calculateResult = calculate($value1, $value2, $operator);
        array_push($numbersArray, $calculateResult);
    }

    return strval(array_pop($numbersArray));
}

// recursive
function eval_expr_rec($expr)
{
    $expr = trim($expr);

    // handle parenthesis
    $subExprNb = 0;
    $subExpr = "";
    $simpleExpr = "";
    for ($i = 0; $i < strlen($expr); ++$i) {

        if ($expr[$i] == '(') {
            if ($subExprNb != 0) {
                // In parenthesis
                $subExpr .= $expr[$i];
            }
            // start of subexpression
            $subExprNb++;
        } else if ($expr[$i] == ')') {
            // end of subexpression
            $subExprNb--;

            if ($subExprNb == 0) {
                // Evaluate subExpr
                $simpleExpr .= eval_expr_rec($subExpr);
                continue;
            } else {
                // In parenthesis
                $subExpr .= $expr[$i];
            }
        } else if ($subExprNb != 0) {
            // In parenthesis
            $subExpr .= $expr[$i];
        }

        if ($subExprNb == 0) {
            // Not between parenthesis
            $simpleExpr .= $expr[$i];
        }
    }

    return calculateExpression($simpleExpr);
}

function eval_expr($expr)
{
    return doubleval(eval_expr_rec($expr));
}

// echo eval_expr("(2+2*(6+7))*3");
