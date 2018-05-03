login()
  .then(parseJWT)
  .then(console.log);

async function login(){
  try {
    const formData = new FormData();
    formData.append('username', 'goran');
    formData.append('password', 'bunneltan');

    const response = await fetch('login', {
      method: 'POST',
      body: formData
    });
    const { token } = await response.json();
    return token;
  } catch (error) {
    console.log(error);
  }
}

async function fetchTodos(token){
  const response = await fetch('api/todos', {
    headers: {
      'Authorization' : `Bearer ${token}`
    }
  });
  const allTodos = response.json();
  return allTodos;
}

/**
 * Function to get the decoded token which will contain the users
 * ID
 */
function parseJWT(token) {
  var base64Url = token.split('.')[1];
  var base64 = base64Url.replace('-', '+').replace('_', '/');
  return JSON.parse(window.atob(base64));
};