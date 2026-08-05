import './bootstrap'
import Alpine from 'alpinejs'
import persist from '@alpinejs/persist'
import focus from '@alpinejs/focus'

// Register plugins
Alpine.plugin(persist)
Alpine.plugin(focus)

// Global Alpine stores
document.addEventListener('alpine:init', () => {
    Alpine.store('app', {
        sidebarCollapsed: Alpine.$persist(false).as('sidebarCollapsed'),
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed
        }
    })
    
    Alpine.store('modal', {
        isOpen: false,
        title: '',
        component: null,
        data: null,
        open(title, component, data = null) {
            this.isOpen = true
            this.title = title
            this.component = component
            this.data = data
        },
        close() {
            this.isOpen = false
            this.component = null
            this.data = null
        }
    })
})

// Start Alpine
Alpine.start()