import type { Directive } from 'vue'

export const ripple: Directive<HTMLElement, void> = {
  mounted(el) {
    el.style.position ||= 'relative'
    el.style.overflow = 'hidden'

    el.addEventListener('click', (e: MouseEvent) => {
      const rect = el.getBoundingClientRect()
      const size = Math.max(rect.width, rect.height)
      const x = e.clientX - rect.left - size / 2
      const y = e.clientY - rect.top - size / 2

      const span = document.createElement('span')
      span.className = 'md3-ripple'
      span.style.width = `${size}px`
      span.style.height = `${size}px`
      span.style.left = `${x}px`
      span.style.top = `${y}px`

      el.appendChild(span)
      span.addEventListener('animationend', () => span.remove())
    })
  },
}
