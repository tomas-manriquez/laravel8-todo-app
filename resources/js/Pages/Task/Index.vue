<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios  from 'axios'
import { Link } from '@inertiajs/inertia-vue3'
import { router } from '@inertiajs/vue3'

//let id = 0
const props = defineProps({ create_url: String })

// Reactive state
const newTodo = reactive({
  id: null,
  name: '',
  description: '',
  start_date: '',
  end_date: '',
  priority: 0,
  is_done: false
})

const todos = ref([])

// Methods
async function addTodo() {
  /**todos.value.push({
    name: newTodo.name,
    description: newTodo.description,
    start_date: newTodo.start_date,
    end_date: newTodo.end_date,
    priority: newTodo.priority,
  })**/
  console.log(newTodo)

  try{
  await axios.post('http://127.0.0.1:8000/api/todo/',newTodo)

  const response = await axios.get('http://127.0.0.1:8000/api/todo/')
  todos.value = response.data
  // Reset form
  newTodo.name = ''
  newTodo.description = ''
  newTodo.start_date =  '',
  newTodo.end_date =  '',
  newTodo.priority = 0

  console.log(todos.value)
  }
  catch (error){
    console.error('Error adding todo:', error)
  }
}

async function fetchTodo() {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/todo/') 
    todos.value = response.data
    //id = todos.value.length > 0 ? Math.max(...todos.value.map(p => p.id)) + 1 : 0
  } catch (error) {
    console.error('Error fetching todos:', error)
  }
}


async function removeTodo(todo) {
  //todos.value = todos.value.filter(t => t.id !== todo.id)
  try{
  await axios.delete(`http://127.0.0.1:8000/api/todo/${todo}`);
  await fetchTodo();
  } catch (error) {
    console.error('Error removing todo:', error)
  }
}

onMounted(fetchTodo)

</script>

<template>
  <h1>Todo Inventory</h1>
  <Store @submit-todo="addTodo"/>

<Link href="/task/create" as="button">Agregar Tarea</Link>


  <p v-if="todos.length > 0">Tarea agregada exitosamente!</p>
  <p v-else>Esperando input...</p>

  <table>
    <caption>Tareas ingresadas</caption>
    <colgroup>
      <col span="4" />
      <col />
    </colgroup>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Prioridad</th>
        <th>Fecha de inicio</th>
        <th>Fecha de término</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="todo in todos" :key="todo.id">
        <td>{{ todo.id }}</td>
        <td>{{ todo.name }}</td>
        <td>{{ todo.description }}</td>
        <td>{{ todo.priority }}</td>
        <td>{{ todo.start_date }}</td>
        <td>{{ todo.end_date }}</td>
        <td>
          <button @click="removeTodo(todo.id)">X</button>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<style scoped>
/* Font and Base */
* {
  font-family: 'Andale Mono', monospace;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

Link, body, html {
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

/* Paragraph feedback */
p {
  text-align: center;
  font-size: 1rem;
}

/* Table Styles */
table {
  width: 100%;
  border-collapse: collapse;
  background-color: #ffffff;
  border: 1px solid #003049;
}

caption {
  caption-side: top;
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
  font-weight: bold;
  color: #003049;
}

th, td {
  border: 1px solid #003049;
  padding: 0.5rem;
  text-align: left;
  font-size: 0.9rem;
}

th {
  background-color: #669bbc;
  color: #fdf0d5;
}

/* Description column expands with content */
td:nth-child(3) {
  width: auto;
  white-space: pre-wrap;
  word-break: break-word;
}

/* Button */
Link,
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

  table {
    font-size: 1rem;
  }

  h1 {
    font-size: 2.5rem;
  }
}
</style>
