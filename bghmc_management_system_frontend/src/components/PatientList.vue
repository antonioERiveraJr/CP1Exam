<template>
    <div class="patient-list">
      <router-link to="/home" class="btn-back">Back to Home</router-link>
      <h2>Patients</h2>
      <div class="patient-list-header">
        <input 
          type="text" 
          v-model="searchQuery" 
          @input="searchPatients" 
          placeholder="Search by name..." 
          class="search-input"
        />
        <router-link to="/patients/create" class="btn-create">Create New Patient</router-link>
      </div>
      <div v-if="loading" class="spinner"></div>
      <ul v-else class="patient-list-items">
        <li v-for="patient in filteredPatients" :key="patient.id" class="patient-item">
          <div class="patient-details">
            <span 
              class="patient-name" 
              @click="handlePatientClick(patient)"
            >
              {{ patient.first_name }} {{ patient.last_name }}
            </span>
            <div class="patient-actions">
              <router-link :to="{ name: 'PatientEdit', params: { id: patient.id } }" class="btn-edit">Edit</router-link>
              <button @click="deletePatient(patient.id)" class="btn-delete">Delete</button>
            </div>
          </div>
          <div v-if="selectedPatient && selectedPatient.id === patient.id && ongoingAdmission" class="admission-info">
            <span><strong>Ward:</strong> {{ ongoingAdmission.ward }}</span>
            <span><strong>Admission Date:</strong> {{ formatDate(ongoingAdmission.datetime_of_admission) }}</span>
            <span v-if="ongoingAdmission.datetime_of_discharge">
              <strong>Discharge Date:</strong> {{ formatDate(ongoingAdmission.datetime_of_discharge) }}
            </span>
          </div>
        </li>
      </ul>
      <div v-if="error" class="error-message">{{ error }}</div>
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  
  export default {
    data() {
      return {
        patients: [],
        searchQuery: '',
        error: null,
        loading: true,
        selectedPatient: null,
        ongoingAdmission: null,
      };
    },
    computed: {
      filteredPatients() {
        const query = this.searchQuery.toLowerCase();
        return this.patients.filter(patient => 
          (`${patient.first_name} ${patient.last_name}`).toLowerCase().includes(query)
        );
      }
    },
    methods: {
      formatDate(dateTime) {
        return new Date(dateTime).toLocaleString();
      },
      fetchPatients() {
        axios.get('http://localhost:8000/api/patients')
          .then(response => {
            this.patients = response.data;
            this.loading = false;
          })
          .catch(error => {
            console.error('Error fetching patients:', error.response ? error.response.data : error.message);
            this.error = 'Failed to load patients. Please try again later.';
            this.loading = false;
          });
      },
      fetchOngoingAdmission(patientId) {
        axios.get(`http://localhost:8000/api/admissions?patient_id=${patientId}`)
          .then(response => {
            const ongoingAdmission = response.data.find(admission => !admission.datetime_of_discharge);
            this.ongoingAdmission = ongoingAdmission || null;
          })
          .catch(error => {
            console.error('Error fetching admissions:', error.response ? error.response.data : error.message);
            this.error = 'Failed to check admissions. Please try again later.';
          });
      },
      handlePatientClick(patient) {
        this.selectedPatient = patient;
        this.fetchOngoingAdmission(patient.id);
        
        if (this.ongoingAdmission) {
          // Show ongoing admission details
          // You might want to set a flag or data property to control the display of this information
          this.$refs.admissionInfoRef.style.display = 'block'; // Example: showing a hidden element
        } else {
          this.$router.push({ name: 'AdmissionCreate', params: { patientId: patient.id } });
        }
      },
      deletePatient(id) {
        axios.delete(`http://localhost:8000/api/patients/${id}`)
          .then(() => {
            this.patients = this.patients.filter(patient => patient.id !== id);
          })
          .catch(error => {
            console.error('Error deleting patient:', error.response ? error.response.data : error.message);
            this.error = 'Failed to delete patient. Please try again later.';
          });
      }
    },
    created() {
      this.fetchPatients();
    }
  };
  </script>
  
  <style scoped>
  .patient-list {
    font-family: 'Arial', sans-serif;
    margin: 20px;
    padding: 20px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    transition: background 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
  }
  
  .patient-list:hover {
    background: #f0f0f0;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
  }
  
  .btn-back {
    display: inline-block;
    padding: 10px 20px;
    background-color: #6c757d;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    margin-bottom: 20px;
    font-weight: bold;
    transition: background-color 0.3s;
  }
  
  .btn-back:hover {
    background-color: #5a6268;
  }
  
  .h2 {
    text-align: center;
    color: #333;
    margin-bottom: 20px;
  }
  
  .patient-list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
  }
  
  .search-input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-right: 10px;
    font-size: 16px;
    transition: border-color 0.3s;
  }
  
  .search-input:focus {
    border-color: #42b983;
  }
  
  .btn-create {
    display: inline-block;
    padding: 10px 20px;
    background-color: #007bff;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
    font-weight: bold;
    transition: background-color 0.3s;
  }
  
  .btn-create:hover {
    background-color: #0056b3;
  }
  
  .spinner {
    width: 40px;
    height: 40px;
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-radius: 50%;
    border-left-color: #42b983;
    animation: spin 1s linear infinite;
    margin: 20px auto;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  .patient-list-items {
    list-style-type: none;
    padding: 0;
    margin: 0;
  }
  
  .patient-item {
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin-bottom: 10px;
    display: flex;
    flex-direction: column;
    transition: background-color 0.3s;
  }
  
  .patient-item:hover {
    background-color: #f9f9f9;
  }
  
  .patient-details {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .patient-name {
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    color: #007bff;
    transition: color 0.3s;
  }
  
  .patient-name:hover {
    color: #0056b3;
  }
  
  .patient-actions {
    display: flex;
    gap: 10px;
  }
  
  .btn-edit,
  .btn-delete {
    padding: 5px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    color: #fff;
    transition: background-color 0.3s;
  }
  
  .btn-edit {
    background-color: #28a745;
  }
  
  .btn-edit:hover {
    background-color: #218838;
  }
  
  .btn-delete {
    background-color: #dc3545;
  }
  
  .btn-delete:hover {
    background-color: #c82333;
  }
  
  .admission-info {
    margin-top: 10px;
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
  }
  
  .error-message {
    margin-top: 10px;
    color: #dc3545;
  }
  </style>
  