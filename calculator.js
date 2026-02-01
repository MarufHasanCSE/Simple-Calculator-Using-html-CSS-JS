// calculator.js

// Simple state variables
let currentNumber = "0"
let expression = ""
let lastInputWasOperator = false

// Display elements
const resultDisplay = document.getElementById("result")
const expressionDisplay = document.getElementById("expression")

// Add a number to the current input
function addNumber(num) {
  // Handle decimal point
  if (num === "." && currentNumber.includes(".")) {
    return
  }

  // Replace initial 0 unless adding decimal
  if (currentNumber === "0" && num !== ".") {
    currentNumber = num
  } else {
    currentNumber += num
  }

  // Reset operator flag
  lastInputWasOperator = false

  // Update display
  resultDisplay.textContent = currentNumber
}

// Add an operator to the expression
function addOperator(op) {
  // Don't add operator if none entered yet
  if (expression === "" && currentNumber === "0") {
    return
  }

  // If last input was operator, replace it
  if (lastInputWasOperator) {
    expression = expression.slice(0, -1) + getOperatorSymbol(op)
  } else {
    // Add current number and operator to expression
    expression += currentNumber + " " + getOperatorSymbol(op) + " "
    currentNumber = "0"
  }

  // Set operator flag
  lastInputWasOperator = true

  // Update displays
  expressionDisplay.textContent = expression
  resultDisplay.textContent = currentNumber
}

// Calculate the result following BODMAS rules
function calculate() {
  // Don't calculate if no expression
  if (expression === "") {
    return
  }

  // Complete the expression with the current number
  const fullExpression = expression + currentNumber

  // Show the full expression with equals sign
  expressionDisplay.textContent = fullExpression + " ="

  try {
    // Parse the expression into numbers and operators
    const parts = fullExpression.split(" ")
    const numbers = []
    const operators = []

    // Extract numbers and operators
    for (let i = 0; i < parts.length; i++) {
      if (i % 2 === 0) {
        // Even indices are numbers
        numbers.push(Number.parseFloat(parts[i]))
      } else {
        // Odd indices are operators
        operators.push(parts[i])
      }
    }

    // First pass: perform multiplication and division (from left to right)
    for (let i = 0; i < operators.length; i++) {
      if (operators[i] === "×" || operators[i] === "÷") {
        if (operators[i] === "×") {
          // Multiplication
          numbers[i] = numbers[i] * numbers[i + 1]
        } else {
          // Division
          if (numbers[i + 1] === 0) {
            throw new Error("Division by zero")
          }
          numbers[i] = numbers[i] / numbers[i + 1]
        }
        // Remove the used number
        numbers.splice(i + 1, 1)
        // Remove the used operator
        operators.splice(i, 1)
        // Adjust index since we removed an item
        i--
      }
    }

    // Second pass: perform addition and subtraction (from left to right)
    let result = numbers[0]
    for (let i = 0; i < operators.length; i++) {
      if (operators[i] === "+") {
        result += numbers[i + 1]
      } else if (operators[i] === "-") {
        result -= numbers[i + 1]
      }
    }

    // Update the result display
    currentNumber = result.toString()
    resultDisplay.textContent = currentNumber

    // Reset expression for next calculation
    expression = ""
    lastInputWasOperator = false
  } catch (error) {
    // Handle errors
    expressionDisplay.textContent = "Error"
    resultDisplay.textContent = "0"
    currentNumber = "0"
    expression = ""
    lastInputWasOperator = false
  }
}

// Clear all inputs and reset calculator
function clearAll() {
  currentNumber = "0"
  expression = ""
  lastInputWasOperator = false
  resultDisplay.textContent = currentNumber
  expressionDisplay.textContent = expression
}

// Helper function to get display symbol for operators
function getOperatorSymbol(op) {
  switch (op) {
    case "*":
      return "×"
    case "/":
      return "÷"
    default:
      return op
  }
}

// Initialize the calculator
clearAll()
