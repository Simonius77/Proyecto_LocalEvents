import { ref, reactive, inject } from 'vue'
import { useRouter } from "vue-router";
import { AbilityBuilder, createMongoAbility } from '@casl/ability';
import { ABILITY_TOKEN } from '@casl/vue';
import { authStore } from "../store/auth";

let user = reactive({
    name: '',
    email: '',
})

export default function useAuth() {
    const processing = ref(false)
    const validationErrors = ref({})
    const router = useRouter()
    const swal = inject('$swal')
    const ability = inject(ABILITY_TOKEN)
    const auth = authStore()

    const loginForm = reactive({
        email: '',
        password: '',
        remember: false
    })

    const forgotForm = reactive({
        email: '',
    })

    const resetForm = reactive({
        email: '',
        token: '',
        password: '',
        password_confirmation: ''
    })

    const registerForm = reactive({
        nombre: '',
        apellidos: '',
        email: '',
        password_confirmation: '',
        // Se inician las coordenadas vacias
        latitud: null,
        longitud: null
    })

    // Funcion para pedir la ubicacion al arrancar
    const getLocation = () => {
        if (!navigator.geolocation) {
            console.warn('Geolocalizacion no soportada por el navegador')
            return
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                registerForm.latitud = position.coords.latitude
                registerForm.longitud = position.coords.longitude
                console.log('Ubicacion obtenida:', registerForm.latitud, registerForm.longitud)
            },
            (error) => {
                console.error('Error al obtener la ubicacion:', error.message)
            }
        )
    }

    // Funcion para procesar el inicio de sesion
    const submitLogin = async () => {
        // Evita multiples clics si ya se esta procesando
        if (processing.value) return

        processing.value = true
        validationErrors.value = {}

        try {
            // IMPORTANTE: Pedimos el token CSRF antes de entrar
            await axios.get('/sanctum/csrf-cookie')

            // Peticion POST a la ruta de login de la API
            const response = await axios.post('/api/login', loginForm)

            // Guardamos el token en el store
            if (response.data.access_token) {
                auth.setToken(response.data.access_token)
            }

            // Si el login es correcto, obtenemos los datos del usuario
            await auth.getUser()
            await loginUser()

            // Mostramos aviso de exito
            swal({
                icon: 'success',
                title: '¡Sesion iniciada!',
                text: 'Bienvenido de nuevo.',
                showConfirmButton: false,
                timer: 1500
            })
            // Redirigimos al panel correspondiente segun el rol del usuario
            // Redirigimos al panel correspondiente segun el rol del usuario de forma robusta
            const roles = auth.user?.roles || []
            const rolString = auth.user?.rol || ''

            if (roles.some(r => r.name?.toLowerCase().includes('admin')) || rolString === 'administrador') {
                await router.push({ name: 'admin.index' });
            } else if (roles.some(r => r.name?.toLowerCase().includes('organizador')) || rolString === 'organizador') {
                await router.push({ name: 'organizador.index' });
            } else {
                await router.push({ name: 'app.dashboard' });
            }
        } catch (error) {
            // Manejo de errores protegidos
            if (error.response?.data) {
                validationErrors.value = error.response.data.errors

                // Aviso visual del error
                swal({
                    icon: 'error',
                    title: 'Fallo al entrar',
                    text: error.response.data.message || 'Verifica tu email y contraseña.',
                })
            } else {
                // Error de red o servidor no disponible
                swal({
                    icon: 'error',
                    title: 'Error de conexion',
                    text: 'No se pudo conectar con el servidor. Revisa tu conexion.',
                })
            }
        } finally {
            // Pase lo que pase, quitamos el estado de carga
            processing.value = false
        }
    }

    // Funcion para procesar el registro de un nuevo usuario
    const submitRegister = async () => {
        // Evita multiples clics si ya se esta procesando
        if (processing.value) return

        processing.value = true
        validationErrors.value = {}

        try {
            // IMPORTANTE: Pedimos el token CSRF antes de registrar para que la peticion sea segura
            await axios.get('/sanctum/csrf-cookie')

            // Peticion POST a la API de registro
            const response = await axios.post('/api/register', registerForm)

            // Si el registro es exitoso, mostramos un mensaje de exito
            swal({
                icon: 'success',
                title: '¡Registro completado!',
                text: 'Ya puedes iniciar sesion con tu cuenta.',
                showConfirmButton: false,
                timer: 3000
            })
            // Redirigimos al usuario a la pantalla de login
            await router.push({ name: 'public.login' })
        } catch (error) {
            // Si hay un error, lo capturamos aqui
            if (error.response?.data) {
                // Errores de validacion
                validationErrors.value = error.response.data.errors

                // Mostramos un aviso visual del fallo
                swal({
                    icon: 'error',
                    title: 'Error en el registro',
                    text: error.response.data.message || 'Por favor, revisa los datos introducidos.',
                })
            } else {
                // Error de conexion o servidor caido
                swal({
                    icon: 'error',
                    title: 'Error de conexion',
                    text: 'No se pudo conectar con el servidor para el registro.',
                })
            }
        } finally {
            // Aseguramos que el boton deje de estar en "cargando"
            processing.value = false
        }
    }

    const submitForgotPassword = async () => {
        if (processing.value) return

        processing.value = true
        validationErrors.value = {}

        await axios.post('/api/forget-password', forgotForm)
            .then(async response => {
                swal({
                    icon: 'success',
                    title: 'We have emailed your password reset link! Please check your mail inbox.',
                    showConfirmButton: false,
                    timer: 1500
                })
                // await router.push({ name: 'admin.index' })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => processing.value = false)
    }

    const submitResetPassword = async () => {
        if (processing.value) return

        processing.value = true
        validationErrors.value = {}

        await axios.post('/api/reset-password', resetForm)
            .then(async response => {
                swal({
                    icon: 'success',
                    title: 'Password successfully changed.',
                    showConfirmButton: false,
                    timer: 1500
                })
                await router.push({ name: 'public.login' })
            })
            .catch(error => {
                if (error.response?.data) {
                    validationErrors.value = error.response.data.errors
                }
            })
            .finally(() => processing.value = false)
    }

    const loginUser = () => {
        console.log('Regenerando reactividad de usuario')
        Object.assign(user, auth.user)
        getAbilities()
    }

    const getUser = async () => {
        const auth = authStore();
        console.log('GettingUser')

        if (auth.authenticated) {
            await auth.getUser()
            await loginUser()
        }
    }

    const getUserSignIn = async () => {
        const auth = authStore();
        console.log('GettingUserSignIn')

        if (auth.authenticated) {
            await auth.getUserSignIn()
            await loginUser()
        }
    }

    // Funcion para cerrar la sesion del usuario
    const logout = async () => {
        if (processing.value) return

        processing.value = true

        // Peticion a la API para invalidar el token
        axios.post('/api/logout')
            .then(response => {
                // La sesion se cerro correctamente en el servidor
            })
            .catch(error => {
                // Mostramos error en consola si falla la peticion
                console.error('Error al cerrar sesion en el servidor:', error)
            })
            .finally(() => {
                // Siempre limpiamos los datos locales del usuario
                Object.assign(user, { name: '', email: '', nombre: '', apellidos: '', roles: [] })
                auth.logout()

                // Redirigimos al usuario a la pantalla de login
                router.push({ name: 'public.login' })

                processing.value = false
            })
    }

    const getAbilities = async () => {
        await axios.get('/api/abilities')
            .then(response => {
                const permissions = response.data
                const { can, rules } = new AbilityBuilder(createMongoAbility)
                can(permissions)
                ability.update(rules)
            })
    }

    return {
        loginForm,
        registerForm,
        forgotForm,
        resetForm,
        validationErrors,
        processing,
        submitLogin,
        submitRegister,
        submitForgotPassword,
        submitResetPassword,
        user,
        getUser,
        getUserSignIn,
        logout,
        getAbilities,
        getLocation
    }
}
