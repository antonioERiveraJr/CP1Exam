<template>
  <div class="container">
    <h2 class="title">Create Patient</h2>
    <form @submit.prevent="createPatient" class="patient-form">
      <div class="form-group">
        <label for="last_name">Last Name:</label>
        <input v-model="patient.last_name" type="text" id="last_name" required placeholder="Enter last name" />
      </div>
      <div class="form-group">
        <label for="first_name">First Name:</label>
        <input v-model="patient.first_name" type="text" id="first_name" required placeholder="Enter first name" />
      </div>
      <div class="form-group">
        <label for="middle_name">Middle Name:</label>
        <input v-model="patient.middle_name" type="text" id="middle_name" placeholder="Enter middle name" />
      </div>
      <div class="form-group">
        <label for="suffix_name">Suffix Name:</label>
        <input v-model="patient.suffix_name" type="text" id="suffix_name" placeholder="Enter suffix name (Jr., Sr., II, III)" />
      </div>
      <div class="form-group">
        <label for="date_of_birth">Date of Birth:</label>
        <input v-model="patient.date_of_birth" type="date" id="date_of_birth" required />
      </div>
      <div class="form-group">
        <label for="address">Address:</label>
        <textarea v-model="patient.address" id="address" rows="4" required placeholder="Enter address"></textarea>
      </div>
      <div class="button-group">
        <button type="submit" class="submit-button">Create Patient</button>
        <button type="button" @click="goBack" class="back-button">Back</button>
      </div>
      <div v-if="error" class="error-message">
        <p>{{ error }}</p>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      patient: {
        last_name: '',
        first_name: '',
        middle_name: '',
        suffix_name: '',
        date_of_birth: '',
        address: '',
      },
      error: null,
    };
  },
  methods: {
    async createPatient() {
      try {
        await axios.post('http://localhost:8000/api/patients', this.patient);
        alert('Patient created successfully!');
        this.$router.push('/patients'); // Redirect to patients list or another page
      } catch (error) {
        if (error.response && error.response.data.errors) {
          console.error('Validation errors:', error.response.data.errors);
          this.error = 'Registration failed. ' + JSON.stringify(error.response.data.errors);
        } else {
          this.error = 'An error occurred during registration. Please try again.';
        }
      }
    },
    goBack() {
      this.$router.push('/patients'); // Redirect to patients list
    },
  },
};
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: 40px auto;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  background-color: #fff;
}

.title {
  text-align: center;
  margin-bottom: 30px;
  font-size: 24px;
  color: #333;
}

.patient-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: bold;
  margin-bottom: 5px;
  color: #555;
}

input, textarea {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 16px;
  background-color: #f9f9f9;
  transition: border-color 0.3s;
}

input:focus, textarea:focus {
  border-color: #007bff;
}

textarea {
  resize: vertical;
}

.button-group {
  display: flex;
  justify-content: space-between;
}

.submit-button, .back-button {
  padding: 12px 20px;
  border: none;
  border-radius: 5px;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button {
  background-color: #4CAF50;
  color: white;
}

.submit-button:hover {
  background-color: #45a049;
}

.back-button {
  background-color: #007bff;
  color: white;
}

.back-button:hover {
  background-color: #0056b3;
}

.error-message {
  color: red;
  text-align: center;
}
</style>
