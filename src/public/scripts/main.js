async function main() {
  const response = await fetch('/todos/1');
  const { data } = await response.json();
  console.log(data);
}

main();