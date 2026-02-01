// calculator.js

// Simple state variables
let currentNumber = "0"
let expression = ""
let lastInputWasOperator = false
let calculationCount = 0
let darkMode = false
let lastResult = ""

// Display elements
const resultDisplay = document.getElementById("result")
const expressionDisplay = document.getElementById("expression")
const historyDisplay = document.getElementById("history")
const statDisplay = document.getElementById("stat-calc")
const bodyEl = document.body
const themeToggle = document.querySelector('.theme-toggle')

// Audio context for sound effects
const audioContext = new (window.AudioContext || window.webkitAudioContext)()

// Function to play a beep sound with different tones
function playBeep(frequency = 800, duration = 100, type = 'sine') {
  try {
    const oscillator = audioContext.createOscillator()
    const gainNode = audioContext.createGain()
    
    oscillator.connect(gainNode)
    gainNode.connect(audioContext.destination)
    
    oscillator.frequency.value = frequency
    oscillator.type = type
    
    gainNode.gain.setValueAtTime(0.15, audioContext.currentTime)
    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + duration / 1000)
    
    oscillator.start(audioContext.currentTime)
    oscillator.stop(audioContext.currentTime + duration / 1000)
  } catch (e) {
    // Silent fail if audio not available
  }
}

// Create particle effect
function createParticle(x, y, color = '#667eea') {
  const particle = document.createElement('div')
  particle.classList.add('particle')
  particle.style.left = x + 'px'
  particle.style.top = y + 'px'
  particle.style.width = '8px'
  particle.style.height = '8px'
  particle.style.backgroundColor = color
  particle.style.borderRadius = '50%'
  particle.style.boxShadow = `0 0 10px ${color}`
  
  const angle = Math.random() * Math.PI * 2
  const distance = 50 + Math.random() * 100
  const tx = Math.cos(angle) * distance
  const ty = Math.sin(angle) * distance
  
  particle.style.setProperty('--tx', tx + 'px')
  particle.style.setProperty('--ty', ty + 'px')
  particle.style.animation = `particle-float ${0.6 + Math.random() * 0.4}s ease-out forwards`
  
  document.getElementById('particles').appendChild(particle)
  
  setTimeout(() => particle.remove(), 1000)
}

// Burst particle effect
function createParticleBurst(x, y, count = 8, color = '#00ff88') {
  for (let i = 0; i < count; i++) {
    setTimeout(() => createParticle(x, y, color), i * 30)
  }
}

// Toggle dark mode
function toggleTheme() {
  darkMode = !darkMode
  bodyEl.classList.toggle('dark-mode')
  const icon = themeToggle.querySelector('.theme-icon')
  icon.textContent = darkMode ? '☀️' : '🌙'
  playBeep(700, 100)
  localStorage.setItem('calculatorTheme', darkMode ? 'dark' : 'light')
}

// Load theme preference
function loadTheme() {
  const saved = localStorage.getItem('calculatorTheme')
  if (saved === 'dark') {
    darkMode = true
    bodyEl.classList.add('dark-mode')
    const icon = themeToggle.querySelector('.theme-icon')
    icon.textContent = '☀️'
  }
}

// Function to add ripple effect on button click
function addRipple(event) {
  const button = event.currentTarget
  const ripple = document.createElement('span')
  const rect = button.getBoundingClientRect()
  const size = Math.max(rect.width, rect.height)
  const x = event.clientX - rect.left - size / 2
  const y = event.clientY - rect.top - size / 2
  
  ripple.style.width = ripple.style.height = size + 'px'
  ripple.style.left = x + 'px'
  ripple.style.top = y + 'px'
  ripple.classList.add('ripple')
  
  button.appendChild(ripple)
  
  setTimeout(() => ripple.remove(), 600)
}

// Add click event listeners to all buttons for ripple effect
document.addEventListener('DOMContentLoaded', () => {
  loadTheme()
  themeToggle.addEventListener('click', toggleTheme)
  
  document.querySelectorAll('button').forEach(button => {
    button.addEventListener('click', addRipple)
  })
  
  // Keyboard support
  document.addEventListener('keydown', handleKeyPress)
})

// Handle keyboard input
function handleKeyPress(event) {
  const key = event.key
  
  if (key >= '0' && key <= '9') {
    addNumber(key)
  } else if (key === '.') {
    addNumber('.')
  } else if (key === '+' || key === '-') {
    addOperator(key)
  } else if (key === '*') {
    addOperator('*')
  } else if (key === '/') {
    event.preventDefault()
    addOperator('/')
  } else if (key === 'Enter' || key === '=') {
    event.preventDefault()
    calculate()
  } else if (key === 'Backspace' || key === 'c' || key === 'C') {
    event.preventDefault()
    clearAll()
  }
}

// Add a number to the current input
function addNumber(num) {
  playBeep(600, 50)
  
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

  // Update display with animation
  resultDisplay.textContent = currentNumber
  resultDisplay.style.animation = 'none'
  setTimeout(() => {
    resultDisplay.style.animation = 'pop 0.2s ease-out'
  }, 10)
  
  // Create particles on larger number input
  if (currentNumber.length % 5 === 0) {
    const rect = resultDisplay.getBoundingClientRect()
    createParticle(rect.left + rect.width / 2, rect.top + rect.height / 2, '#667eea')
  }
}

// Add an operator to the expression
function addOperator(op) {
  playBeep(1000, 80)
  
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

  // Update displays with animation
  expressionDisplay.textContent = expression
  expressionDisplay.style.animation = 'none'
  setTimeout(() => {
    expressionDisplay.style.animation = 'slideUp 0.3s ease-out'
  }, 10)
  resultDisplay.textContent = currentNumber
}

// Calculate the result following BODMAS rules
function calculate() {
  playBeep(1200, 150)
  
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

    // Update the result display with animation
    lastResult = currentNumber
    currentNumber = result.toString()
    resultDisplay.textContent = currentNumber
    resultDisplay.style.animation = 'none'
    setTimeout(() => {
      resultDisplay.style.animation = 'pop 0.4s ease-out'
    }, 10)

    // Update history
    historyDisplay.textContent = `Last: ${lastResult}`
    historyDisplay.style.animation = 'slideUp 0.3s ease-out'

    // Increment calculation count
    calculationCount++
    statDisplay.textContent = `Calculations: ${calculationCount}`

    // Create particle burst for successful calculation
    const rect = resultDisplay.getBoundingClientRect()
    createParticleBurst(rect.left + rect.width / 2, rect.top + rect.height / 2, 12, '#00ff88')

    // Reset expression for next calculation
    expression = ""
    lastInputWasOperator = false
  } catch (error) {
    // Handle errors
    playBeep(300, 200)
    expressionDisplay.textContent = "Error"
    resultDisplay.textContent = "0"
    currentNumber = "0"
    expression = ""
    lastInputWasOperator = false
    
    // Create red particle burst for error
    const rect = resultDisplay.getBoundingClientRect()
    createParticleBurst(rect.left + rect.width / 2, rect.top + rect.height / 2, 8, '#ff6b6b')
  }
}

// Clear all inputs and reset calculator
function clearAll() {
  playBeep(400, 80)
  currentNumber = "0"
  expression = ""
  lastInputWasOperator = false
  resultDisplay.textContent = currentNumber
  expressionDisplay.textContent = expression
  historyDisplay.textContent = ""
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
