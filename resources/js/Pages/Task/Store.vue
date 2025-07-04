<script setup>
import { reactive } from 'vue'
import { defineEmits } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import { router } from '@inertiajs/vue3'

const props = defineProps({ index_url: String })
const emit = defineEmits(['submit-todo'])

const newTodo = reactive({
  id: null,
  name: '',
  description: '',
  start_date: '',
  end_date: '',
  priority: 0,
  is_done: false,
})

async function submitForm() {
  emit('submit-todo', { ...newTodo })
  console.log(newTodo)

  try{
  await axios.post('http://127.0.0.1:8000/api/todo/',newTodo)

  // Reset form
  newTodo.name = ''
  newTodo.description = ''
  newTodo.start_date =  '',
  newTodo.end_date =  '',
  newTodo.priority = 0

  }
  catch (error){
    console.error('Error adding todo:', error)
  }
}
</script>

<template>

<Link href="/task/" as="button">Volver a Menu</Link>
  <form @submit.prevent="submitForm">
    <fieldset>
      <label for="name">Nombre</label>
      <input
        id="name"
        v-model="newTodo.name"
        type="text"
        required
        placeholder="Ingrese el nombre de la tarea"
      />

      <label for="description">Descripción</label>
      <textarea
        id="description"
        v-model="newTodo.description"
        rows="3"
        cols="50"
        placeholder="Describe la tarea"
      ></textarea>

      <label for="start_date">Fecha de Inicio</label>
      <input
        id="start_date"
        v-model="newTodo.start_date"
        type="date"
        required
      />

      <label for="end_date">Fecha de Término</label>
      <input
        id="end_date"
        v-model="newTodo.end_date"
        type="date"
        required
      />

      <label for="priority">Prioridad (0 hasta 3)</label>
      <input
        id="priority"
        v-model="newTodo.priority"
        type="number"
        min="0"
        max="3"
        required
      />

      <input type="submit" value="Agregar Tarea" />
    </fieldset>
  </form>
</template>

<style scoped>
/* Font and Base */
* {
  font-family: 'Andale Mono', monospace;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body, html {
  width: 100%;
  height: 100%;
  background-color: #fdf0d5;
  color: #003049;
}

h1 {
  font-size: 2rem;
  margin-bottom: 1rem;
  color: #780000;
  text-align: center;
}

/* Layout */
form, table, p, h1 {
  max-width: 90%;
  margin: 0 auto 2rem auto;
}

form {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  background-color: #669bbc20;
  padding: 1.5rem;
  border-radius: 0.5rem;
  border: 1px solid #669bbc;
}

/* Inputs */
input[type="text"],
input[type="date"],
input[type="number"],
textarea {
  width: 100%;
  padding: 0.5rem;
  font-size: 1rem;
  border: 1px solid #c1121f;
  border-radius: 0.25rem;
  background-color: #ffffff;
  color: #003049;
}

input[type="submit"] {
  background-color: #c1121f;
  color: #fdf0d5;
  border: none;
  padding: 0.75rem;
  border-radius: 0.25rem;
  font-weight: bold;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

input[type="submit"]:hover {
  background-color: #780000;
}
/* Button */
button {
  background-color: #c1121f;
  color: #fdf0d5;
  border: none;
  padding: 0.5rem 0.75rem;
  border-radius: 0.25rem;
  cursor: pointer;
}

button:hover {
  background-color: #780000;
}

/* Responsive behavior */
@media (min-width: 768px) {
  form {
    max-width: 600px;
  }
}
</style>