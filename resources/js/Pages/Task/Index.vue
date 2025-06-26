<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios  from 'axios'

//let id = 0

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
  <form @submit.prevent="addTodo">
    <fieldset>
      Nombre
      <input
        id="name"
        v-model="newTodo.name"
        type="text"
        required
        placeholder="Ingrese el nombre de la tarea"
      />
      <br />
      Descripción
      
      <textarea name="description" v-model="newTodo.description" rows="3" cols="50">Write something here</textarea>
      <br />
      Fecha de Inicio
      <input
        id="start_date"
        v-model="newTodo.start_date"
        type="date"
        required
      />
      <br />
     <br />
      Fecha de Término
      <input
        id="end_date"
        v-model="newTodo.end_date"
        type="date"
        required
      />
      <br /> 
      <br> 
      Prioridad (0 hasta 3)
      <input 
        id="priority"
        v-model="newTodo.priority"
        type="number"
        min="0"
        max="3"
        required
      />
      <br />
      <input type="submit" value="Agregar Tarea" />
    </fieldset>
  </form>

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
