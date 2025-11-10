async function parseErrors(error) {
  if (error instanceof Error) {
    return {}
  }

  if (error.status === 422) {
    const data = await error.json()
    return data.errors
  }

  return {}
}

export default parseErrors
