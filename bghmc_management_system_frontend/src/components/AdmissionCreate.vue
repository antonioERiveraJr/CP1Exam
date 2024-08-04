<template>
  <div class="admission-create">
    <h2>Create Admission</h2>
    <form @submit.prevent="checkExistingAdmission">
      <div class="form-group">
        <label for="patient_id">Patient ID:</label>
        <input v-model="admission.patient_id" type="text" id="patient_id" disabled />
      </div>
      <div class="form-group">
        <label for="ward">Ward:</label>
        <input v-model="admission.ward" type="text" id="ward" />
        <span v-if="errors.ward" class="error-message">{{ errors.ward }}</span>
      </div>
      <div class="form-group">
        <label for="datetime_of_admission">Date & Time of Admission:</label>
        <input v-model="admission.datetime_of_admission" type="datetime-local" id="datetime_of_admission" />
        <span v-if="errors.datetime_of_admission" class="error-message">{{ errors.datetime_of_admission }}</span>
      </div>
      <div v-if="existingAdmission" class="existing-admission-info">
        <p>This patient is already admitted:</p>
        <p><strong>Ward:</strong> {{ existingAdmission.ward }}</p>
        <p><strong>Admission Date:</strong> {{ formatDate(existingAdmission.datetime_of_admission) }}</p>
      </div>
      <div class="form-actions">
        <button v-if="!existingAdmission" type="submit">Create Admission</button>
        <router-link to="/home" class="btn-back">Back</router-link>
      </div>
      <div v-if="successMessage" class="success-message">
        {{ successMessage }}
      </div>
      <div v-if="error" class="error-message">
        {{ error }}
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      admission: {
        patient_id: this.$route.params.patientId || '',
        ward: '',
        datetime_of_admission: ''
      },
      existingAdmission: null,
      error: null,
      successMessage: null,
      errors: {
        ward: null,
        datetime_of_admission: null
      }
    };
  },
  methods: {
    formatDate(dateTime) {
      return new Date(dateTime).toLocaleString();
    },
    checkExistingAdmission() {
      // Reset errors
      this.errors.ward = null;
      this.errors.datetime_of_admission = null;

      // Validate fields
      if (!this.admission.ward) {
      this.errors.ward = 'Ward is required.';
      setTimeout(() => {
        this.errors.ward = null;
      }, 2000);
      return;
      }
      if (!this.admission.datetime_of_admission) {
      this.errors.datetime_of_admission = 'Date & Time of Admission is required.';
      
      setTimeout(() => {
        console.log('Clearing error...');
        this.errors.datetime_of_admission = null;
      }, 2000);
        return;
      }

      // Fetch admissions where patient_id matches
      axios.get(`http://localhost:8000/api/admissions?patient_id=${this.admission.patient_id}`)
        .then(response => {
          // Check if any record has datetime_of_discharge as null
          const hasOngoingAdmission = response.data.some(admission => admission.patient_id === this.admission.patient_id && admission.datetime_of_discharge === null);

          if (hasOngoingAdmission) {
            // If any ongoing admission is found, show a message
            alert('This patient already has an ongoing admission. Cannot create another admission.');
            setTimeout(() => {
            this.error = null;
          }, 5000);
          } else {
            // Proceed with creating a new admission if no ongoing admission is found
            this.createAdmission();
          }
        })
        .catch(error => {
          console.error('Error checking existing admission:', error.response ? error.response.data : error.message);
          this.error = 'Failed to check existing admission. Please try again later.';
        });
    },
    createAdmission() {
      axios.post('http://localhost:8000/api/admissions', this.admission)
        .then(() => {
          this.successMessage = 'Admission successfully created!';
          this.error = null;
          setTimeout(() => {
            this.$router.push('/home');
          }, 2000); // Redirect after 2 seconds
        })
        .catch(error => {
          console.error('Error creating admission:', error.response ? error.response.data : error.message);
          this.error = 'Failed to create admission. Please try again later.';
          this.successMessage = null;
        });
    }
  }
};
</script>

<style scoped>
.admission-create {
  font-family: Arial, sans-serif;
  margin: 20px;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background-color: #f9f9f9;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

h2 {
  font-size: 24px;
  margin-bottom: 20px;
}

form {
  display: flex;
  flex-direction: column;
}

.form-group {
  margin-bottom: 15px;
}

label {
  display: block;
  font-weight: bold;
  margin-bottom: 5px;
}

input {
  width: 100%;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 16px;
}

button {
  padding: 10px 15px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
}

button:hover {
  background-color: #0056b3;
}

.btn-back {
  display: inline-block;
  margin-top: 10px;
  padding: 10px 15px;
  background-color: #6c757d;
  color: white;
  text-decoration: none;
  border-radius: 5px;
  font-size: 16px;
}

.btn-back:hover {
  background-color: #5a6268;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.existing-admission-info {
  margin-top: 10px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  background-color: #f9f9f9;
}

.success-message {
  margin-top: 20px;
  padding: 10px;
  border: 1px solid #28a745;
  border-radius: 5px;
  background-color: #d4edda;
  color: #155724;
}

.error-message {
  margin-top: 20px;
  padding: 10px;
  border: 1px solid #dc3545;
  border-radius: 5px;
  background-color: #f8d7da;
  color: #721c24;
}
</style>
