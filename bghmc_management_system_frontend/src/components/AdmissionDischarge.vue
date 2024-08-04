<template>
  <div class="admission-discharge">
    <button @click="goBack" class="btn-back">Back to Admissions List</button>
    <h2>Admission Details</h2>

    <div v-if="loading" class="loading-spinner">
      <div class="spinner"></div>
      <span>Loading details...</span>
    </div>

    <div v-if="!loading && admission && patient" class="details-container">
      <div class="patient-details">
        <h3>Patient Details</h3>
        <p><strong>Patient ID:</strong> {{ admission.patient_id }}</p>
        <p><strong>Name:</strong> {{ patient.first_name }} {{ patient.middle_name }} {{ patient.last_name }} {{ patient.suffix_name }}</p>
      </div>

      <div class="admission-details">
        <h3>Admission Information</h3>
        <p><strong>Ward:</strong> {{ admission.ward }}</p>
        <p><strong>Admission Date:</strong> {{ formatDate(admission.datetime_of_admission) }}</p>
        <p v-if="admission.datetime_of_discharge"><strong>Discharge Date:</strong> {{ formatDate(admission.datetime_of_discharge) }}</p>
      </div>

      <div class="discharge-container" v-if="!admission.datetime_of_discharge">
        <input v-model="dischargeDateTime" type="datetime-local" />
        <button @click="dischargeAdmission" class="btn-discharge">Discharge</button>
      </div>
    </div>

    <div v-if="error" class="error-message">{{ error }}</div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      admission: null,
      patient: null,
      dischargeDateTime: '',
      error: null,
      loading: false,
    };
  },
  created() {
    this.fetchAdmissionDetails();
  },
  methods: {
    formatDate(dateTime) {
      return new Date(dateTime).toLocaleString();
    },
    fetchAdmissionDetails() {
      this.loading = true;
      axios.get(`http://localhost:8000/api/admissions/${this.$route.params.id}`)
        .then(response => {
          this.admission = response.data;
          this.fetchPatientDetails(this.admission.patient_id);
        })
        .catch(error => {
          console.error('Error fetching admission details:', error.response ? error.response.data : error.message);
          this.error = 'Failed to load admission details. Please try again later.';
          this.loading = false;
        });
    },
    fetchPatientDetails(patientId) {
      axios.get(`http://localhost:8000/api/patients/${patientId}`)
        .then(response => {
          this.patient = response.data;
          this.loading = false;
        })
        .catch(error => {
          console.error('Error fetching patient details:', error.response ? error.response.data : error.message);
          this.error = 'Failed to load patient details. Please try again later.';
          this.loading = false;
        });
    },
    dischargeAdmission() {
      if (this.dischargeDateTime) {
        axios.patch(`http://localhost:8000/api/admissions/${this.$route.params.id}/discharge`, {
          datetime_of_discharge: this.dischargeDateTime
        })
        .then(() => {
          this.fetchAdmissionDetails();
        })
        .catch(error => {
          console.error('Error discharging admission:', error.response ? error.response.data : error.message);
          this.error = 'Failed to discharge admission. Please try again later.';
        });
      } else {
        this.error = 'Please enter a valid discharge date and time.';
      }
    },
    goBack() {
      this.$router.push('/admissions');
    }
  }
};
</script>

<style scoped>
.admission-discharge {
  font-family: Arial, sans-serif;
  margin: 20px;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 10px;
  background-color: #f9f9f9;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
  position: relative;
}

.btn-back, .btn-discharge {
  margin-bottom: 10px;
  padding: 8px 12px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}

.btn-back {
  background-color: #007bff;
  color: white;
}

.btn-back:hover {
  background-color: #0056b3;
}

.btn-discharge {
  background-color: #dc3545;
  color: white;
}

.btn-discharge:hover {
  background-color: #c82333;
}

.details-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.patient-details, .admission-details {
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 5px;
  background-color: #fff;
}

.patient-details h3, .admission-details h3 {
  margin-top: 0;
}

.loading-spinner {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 20px;
  font-size: 16px;
  color: #007bff;
}

.spinner {
  width: 24px;
  height: 24px;
  border: 4px solid rgba(0, 123, 255, 0.3);
  border-top: 4px solid #007bff;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 10px;
}

.error-message {
  color: #dc3545;
  margin-top: 10px;
}

.discharge-container {
  display: flex;
  align-items: center;
  gap: 10px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
