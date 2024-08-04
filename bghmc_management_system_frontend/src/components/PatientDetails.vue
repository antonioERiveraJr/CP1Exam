<template>
    <div class="patient-details">
      <router-link to="/patients" class="btn-back">Back to Patient List</router-link>
      <h2>Patient Details</h2>
      <div v-if="patient">
        <p><strong>ID:</strong> {{ patient.id }}</p>
        <p><strong>Name:</strong> {{ patient.first_name }} {{ patient.last_name }}</p>
        <p><strong>Date of Birth:</strong> {{ formatDate(patient.date_of_birth) }}</p>
        <p><strong>Address:</strong> {{ patient.address }}</p>
  
        <div v-if="ongoingAdmission">
          <h3>Ongoing Admission</h3>
          <p><strong>Ward:</strong> {{ ongoingAdmission.ward }}</p>
          <p><strong>Admission Date:</strong> {{ formatDate(ongoingAdmission.datetime_of_admission) }}</p>
          <p v-if="ongoingAdmission.datetime_of_discharge">
            <strong>Discharge Date:</strong> {{ formatDate(ongoingAdmission.datetime_of_discharge) }}
          </p>
        </div>
  
        <div class="patient-actions">
          <router-link :to="{ name: 'PatientEdit', params: { id: patient.id } }" class="btn-edit">Edit</router-link>
          <button @click="admitPatient" class="btn-admit">Admit Patient</button>
          <button @click="deletePatient" class="btn-delete">Delete</button>
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
        patient: null,
        ongoingAdmission: null,
        error: null,
      };
    },
    computed: {
      patientId() {
        return this.$route.params.id;
      }
    },
    methods: {
      formatDate(dateTime) {
        return new Date(dateTime).toLocaleString();
      },
      fetchPatient() {
        axios.get(`http://localhost:8000/api/patients/${this.patientId}`)
          .then(response => {
            this.patient = response.data;
            this.fetchOngoingAdmission();
          })
          .catch(error => {
            console.error('Error fetching patient:', error.response ? error.response.data : error.message);
            this.error = 'Failed to load patient details. Please try again later.';
          });
      },
      fetchOngoingAdmission() {
        axios.get(`http://localhost:8000/api/admissions?patient_id=${this.patientId}`)
          .then(response => {
            const ongoingAdmission = response.data.find(admission => !admission.datetime_of_discharge);
            this.ongoingAdmission = ongoingAdmission || null;
          })
          .catch(error => {
            console.error('Error fetching admissions:', error.response ? error.response.data : error.message);
            this.error = 'Failed to check admissions. Please try again later.';
          });
      },
      admitPatient() {
        if (!this.ongoingAdmission) {
          this.$router.push({ name: 'AdmissionCreate', params: { patientId: this.patientId } });
        } else {
          this.error = 'Patient is already admitted.';
        }
      },
      deletePatient() {
        axios.delete(`http://localhost:8000/api/patients/${this.patientId}`)
          .then(() => {
            this.$router.push('/patients');
          })
          .catch(error => {
            console.error('Error deleting patient:', error.response ? error.response.data : error.message);
            this.error = 'Failed to delete patient. Please try again later.';
          });
      }
    },
    created() {
      this.fetchPatient();
    }
  };
  </script>
  
  <style scoped>
  .patient-details {
    font-family: Arial, sans-serif;
    margin: 20px;
    padding: 10px;
  }
  
  .btn-back {
    padding: 10px 15px;
    background-color: #6c757d;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
    margin-bottom: 20px;
  }
  
  .btn-back:hover {
    background-color: #5a6268;
  }
  
  .patient-actions {
    margin-top: 20px;
  }
  
  .btn-edit {
    padding: 5px 25px;
    background-color: #28a745;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    margin-right: 10px;
  }
  
  .btn-admit {
    padding: 5px 25px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    margin-right: 10px;
  }
  
  .btn-delete {
    padding: 5px 25px;
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 5px;
  }
  
  .btn-delete:hover {
    background-color: #c82333;
  }
  
  .error-message {
    color: #dc3545;
    margin-top: 10px;
  }
  </style>
  