<template>
  <div class="patient-details">
    <router-link to="/patients" class="btn-back">Back to Patient List</router-link>
    <h2>Patient Details</h2>
    <div v-if="patient" class="patient-info">
      <p><strong>ID:</strong> {{ patient.id }}</p>
      <p><strong>Name:</strong> {{ patient.first_name }} {{ patient.last_name }}</p>
      <p><strong>Date of Birth:</strong> {{ formatDate(patient.date_of_birth) }}</p>
      <p><strong>Address:</strong> {{ patient.address }}</p>

      <div v-if="ongoingAdmission" class="admission-info">
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
  font-family: 'Roboto', sans-serif;
  max-width: 600px;
  margin: 40px auto;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  background-color: #fff;
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
  transition: background-color 0.3s;
}

.btn-back:hover {
  background-color: #5a6268;
}

.patient-info p {
  margin: 10px 0;
  font-size: 16px;
}

.admission-info {
  margin-top: 20px;
  padding: 10px;
  border-top: 1px solid #ddd;
}

.patient-actions {
  margin-top: 20px;
  display: flex;
  justify-content: space-between;
}

.btn-edit, .btn-admit, .btn-delete {
  padding: 10px 20px;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.btn-edit {
  background-color: #28a745;
}

.btn-edit:hover {
  background-color: #218838;
}

.btn-admit {
  background-color: #007bff;
}

.btn-admit:hover {
  background-color: #0056b3;
}

.btn-delete {
  background-color: #dc3545;
}

.btn-delete:hover {
  background-color: #c82333;
}

.error-message {
  color: #dc3545;
  margin-top: 10px;
}
</style>
