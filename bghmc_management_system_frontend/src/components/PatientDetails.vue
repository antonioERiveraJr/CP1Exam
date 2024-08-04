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
        <button @click="showDeleteModal" class="btn-delete">Delete</button>
      </div>
    </div>
    <div v-if="error" class="error-message">{{ error }}</div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <h3>Confirm Deletion</h3>
        <p>Please enter the password to confirm deletion:</p>
        <input v-model="password" type="password" placeholder="Password" />
        <button @click="confirmDeletion" class="btn-confirm">Confirm</button>
        <button @click="cancelDeletion" class="btn-cancel">Cancel</button>
      </div>
    </div>
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
      showModal: false,
      password: '',
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
    showDeleteModal() {
      this.showModal = true;
    },
    cancelDeletion() {
      this.showModal = false;
      this.password = '';
    },
    confirmDeletion() {
      // Replace this with actual password verification logic
      if (this.password === 'admin') {
        axios.delete(`http://localhost:8000/api/patients/${this.patientId}`)
          .then(() => {
            this.$router.push('/patients');
          })
          .catch(error => {
            console.error('Error deleting patient:', error.response ? error.response.data : error.message);
            this.error = 'Failed to delete patient. Please try again later.';
          });
        this.showModal = false;
        this.password = '';
      } else {
        this.error = 'Incorrect password. Please try again.';
        setTimeout(() => {
            this.error = null;
          }, 2000);
      }
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
  max-width: 700px;
  margin: 40px auto;
  padding: 20px;
  border-radius: 15px;
  box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
  background-color: #fdfdfd;
}

.btn-back {
  padding: 12px 18px;
  background-color: #6c757d;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: bold;
  display: inline-block;
  margin-bottom: 20px;
  transition: background-color 0.3s, transform 0.3s;
}

.btn-back:hover {
  background-color: #5a6268;
  transform: scale(1.02);
}

.patient-info p {
  margin: 12px 0;
  font-size: 18px;
  color: #333;
}

.admission-info {
  margin-top: 20px;
  padding: 15px;
  border-top: 2px solid #ddd;
  background-color: #f8f9fa;
  border-radius: 10px;
}

.patient-actions {
  margin-top: 20px;
  display: flex;
  gap: 15px;
}

.btn-edit, .btn-admit, .btn-delete {
  padding: 12px 24px;
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s, transform 0.3s;
}

.btn-edit {
  background-color: #28a745;
}

.btn-edit:hover {
  background-color: #218838;
  transform: scale(1.02);
}

.btn-admit {
  background-color: #007bff;
}

.btn-admit:hover {
  background-color: #0056b3;
  transform: scale(1.02);
}

.btn-delete {
  background-color: #dc354a;
}

.btn-delete:hover {
  background-color: #c82333;
  transform: scale(1.02);
}

.error-message {
  color: #dc3545;
  margin-top: 10px;
  font-size: 16px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 100%;
  text-align: center;
}

.modal input {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-top: 10px;
  margin-bottom: 20px;
  width: calc(100% - 20px);
}

.btn-confirm, .btn-cancel {
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
  margin: 5px;
  transition: background-color 0.3s, transform 0.3s;
}

.btn-confirm {
  background-color: #28a745;
  color: white;
}

.btn-confirm:hover {
  background-color: #218838;
  transform: scale(1.02);
}

.btn-cancel {
  background-color: #dc354a;
  color: white;
}

.btn-cancel:hover {
  background-color: #c82333;
  transform: scale(1.02);
}
</style>
