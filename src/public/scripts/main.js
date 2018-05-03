function main(){
  fetch('api/todos/1')
    .then(res => res.json())
    .then(console.log);
}

function getAllUsers(){
  fetch('api/users')
    .then(res => res.json())
    .then(console.log);
}

function postTodo(){
  // x-www-form-urlencoded
  const formData = new FormData();
  const todoInput = document.getElementById('todoInput');
  formData.append('content', todoInput.value);

  const postOptions = {
    method: 'POST',
    body: formData
  }

  fetch('api/todos', postOptions)
    .then(res => res.json())
    .then((newTodo) => {
        document.body.insertAdjacentHTML('beforeend', newTodo.data.content);
    });
}


function login(){
  const formData = new FormData();
  formData.append('username', 'goran');
  formData.append('password', 'bunneltan');
  const postOptions = {
    method: 'POST',
    body: formData
  }

  fetch('/login', postOptions)
    .then(res => res.json())
    .then(console.log);
}

login();


const addTodoButton = document.getElementById('addTodo');
addTodoButton.addEventListener('click', postTodo);