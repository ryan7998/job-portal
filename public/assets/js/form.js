// Real-time title uniqueness check:
const checkTitleUnique = async (title) => {
    const response = await fetch('public?controller=job&action=checkTitleUnique&title=' + encodeURIComponent(title))
    const data = await response.json()
    return data.available
}

// State Autocomplete
const populateStates = async (country) => {
    const response = await fetch('data/states.json')
    const statesData = await response.json()
    const states = statesData[country] || []
    const stateSelect = document.getElementById('state')

    stateSelect.innerHTML = '<option value="">Select your state/province</option>'
    if(!states)
        return
    states.forEach(state => {
        const option = document.createElement('option')
        option.value = state
        option.textContent = state
        stateSelect.appendChild(option)
    })
}

document.addEventListener('DOMContentLoaded', function() {
    // if js is enabled let php know about it:
    document.getElementById('js_enabled').value = 'true';

    // Get form and input elements
    const form = document.getElementById('projectForm')
    const titleInput = document.getElementById('title')
    const scriptTextarea = document.getElementById('script')
    const countrySelect = document.getElementById('country')
    const stateSelect = document.getElementById('state')
    const fileInput = document.getElementById('file');
    const fileNameDisplay = document.querySelector('.file-upload-wrapper .file-name');
    const budgetInputs = document.querySelectorAll('input[name="budget"]')
    const budgetLabels = document.querySelectorAll('#budget-button-group label')
    

    // Error message containers
    const errorTitle = document.getElementById('error_title')
    const errorCountry = document.getElementById('error_country')
    const errorState = document.getElementById('error_state')
    const errorBudget  = document.getElementById('error_budget')
    const errorFile  = document.getElementById('error_file')
    const wordCountDisplay  = document.getElementById('wordCount')

    // As js is enabled remove all the states set by php and make this the default:
    stateSelect.innerHTML = '<option value="">Select your state/province</option>'

    // Real-time validation for title
    titleInput.addEventListener('blur', function() {
        if (titleInput.value.trim() === '') {
            titleInput.classList.add('error-highlight')
            errorTitle.textContent = 'Project name is required.'
            titleInput.setAttribute('aria-invalid', 'true')
        } else {
            titleInput.classList.remove('error-highlight')
            errorTitle.textContent = ''
            titleInput.setAttribute('aria-invalid', 'false')
            checkTitleUnique(titleInput.value.trim()).then(titleAvailable=>{
                if(!titleAvailable){
                    errorTitle.textContent = 'Project name is already taken. Try with a different name.'
                    titleInput.setAttribute('aria-invalid', 'true')
                }
            })
        }
    })

    // Live word count for script:
    scriptTextarea.addEventListener('input', function() {
        const words = scriptTextarea.value.trim().split(/\s+/).filter(word=> word.length > 0)
        wordCountDisplay.textContent = words.length + " words"
    })

    // Validate country selection and populate state options
    countrySelect.addEventListener('change', function() {
        if(!countrySelect.value){
            countrySelect.classList.add('error-highlight')
            errorCountry.textContent = 'Please select a country.'
            countrySelect.setAttribute('aria-invalid', 'true')
        } else {
            countrySelect.classList.remove('error-highlight')
            errorCountry.textContent = ''
            countrySelect.setAttribute('aria-invalid', 'false')
            populateStates(countrySelect.value)
        }
    })
    
    // Validate State selection
    stateSelect.addEventListener('change', function() {
        if (!stateSelect.value) {
            stateSelect.classList.add('error-highlight')
            errorState.textContent = 'Please select your state/province.'
            stateSelect.setAttribute('aria-invalid', 'true')
        } else {
            stateSelect.classList.remove('error-highlight')
            errorState.textContent = ''
            stateSelect.setAttribute('aria-invalid', 'false')
        }
    })
  
    fileInput.addEventListener('change', function(e) {
      const files = e.target.files;
      if (files.length > 0) {
        // Display the first selected file's name
        fileNameDisplay.textContent = files[0].name;
      } else {
        fileNameDisplay.textContent = '';
      }
    });

    // Validate Budget selection on change
    budgetInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            errorBudget.textContent = ''
            budgetLabels.forEach(function(label) {
                label.classList.remove('error-highlight')
            })
        })
    })

    // Final form submission validation
    form.addEventListener('submit', async (event) => {
        event.preventDefault()
        let valid = true

        if (titleInput.value.trim() === '') {
            titleInput.classList.add('error-highlight')
            errorTitle.textContent = 'Project name is required.'
            titleInput.setAttribute('aria-invalid', 'true')
            valid = false
        }
        if (countrySelect.value === '') {
            countrySelect.classList.add('error-highlight')
            errorCountry.textContent = 'Please select a country.'
            countrySelect.setAttribute('aria-invalid', 'true')
            valid = false
        }
        if (stateSelect.value === '' || stateSelect.value === 'Select your state/province') {
            stateSelect.classList.add('error-highlight')
            errorState.textContent = 'Please select your state/province.'
            stateSelect.setAttribute('aria-invalid', 'true')
            valid = false
        }
        let budgetSelected = Array.from(budgetInputs).some(input => input.checked)
        if (!budgetSelected) {
            budgetLabels.forEach(function(label) {
                label.classList.add('error-highlight')
            })
            errorBudget.textContent = 'Please select your budget.'
            valid = false
        }

        // Prevent submission if any field is invalid
        if (!valid) return

        const form = event.target
        const formData = new FormData(form)

        try {
            // Send the AJAX request using fetch:
            const response = await fetch('/public?controller=job&action=handleSubmit', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-Token': formData.get('csrf_token')
                }
            })

            // console.log('response: ', response)
            // Ensure the response is in JSON format
            const result = await response.json();
            // Handle errors from the server
            if (result.error || result.errors) {
                // Display error messages next to the corresponding fields
                if(result.errors){
                    // For example, if result.errors is an object with keys matching your field names:
                    for (const [field, message] of Object.entries(result.errors)) {
                        const errorElement = document.getElementById(`error_${field}`);
                        if (errorElement) {
                            errorElement.textContent = message;
                        }
                    }
                } else {
                    alert(result.error);
                }
            } else if (result.success) {
                // Optionally, show a success message or redirect
                alert(result.success);
                //clear the form or redirect:
                form.reset();
                errorFile.textContent=''
            }
            
        } catch (error) {
            // Network or unexpected errors can be handled here
            console.error('AJAX submission error:', error);
            alert('There was a problem submitting the form. Please try again later.');
        } finally {
            // Hide the progress indicator
            // loadingIndicator.style.display = 'none';
        }

    })

    form.addEventListener('reset', async (event) => {
        errorTitle.textContent = '';
        errorCountry.textContent = '';
        errorState.textContent = '';
        errorBudget.textContent = '';

        // Reset ARIA attributes and error-highlight classes for inputs
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.setAttribute('aria-invalid', 'false');
            input.classList.remove('error-highlight')
        });

        // Reset ARIA attributes and error-highlight classes for budget:
        budgetLabels.forEach(label => {
            label.classList.remove('error-highlight')
        })

        // Clear the live word count
        wordCountDisplay.textContent = '0 words';

        // Clear the custom file name display
        if (fileNameDisplay) {
            fileNameDisplay.textContent = '';
        }

        // reset any dynamically populated select options (e.g., state)
        stateSelect.innerHTML = '<option value="">Select your state/province</option>';

        
    })
})