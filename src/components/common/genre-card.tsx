import Link from "next/link"
import Image from "next/image"
import type { Genre } from "@/types/tmdb"
import { PlaceHolderImages } from "@/lib/placeholder-images"
import { Card } from "../ui/card"

interface GenreCardProps {
  genre?: Genre | null
  type?: "movie" | "tv"
}

export function GenreCard({ genre, type = "movie" }: GenreCardProps) {
  if (!genre || !genre.id) {
    return null
  }

  const placeholder = PlaceHolderImages.find((p) => p.id === String(genre.id))
  const imageUrl = placeholder ? placeholder.imageUrl : `https://picsum.photos/seed/${genre.id}/500/300`
  const imageHint = placeholder ? placeholder.imageHint : "movie poster"

  return (
    <Link href={`/genre/${genre.id}?type=${type}`} className="group block">
      <Card className="overflow-hidden relative aspect-video rounded-lg transition-all duration-300 ease-in-out hover:shadow-lg hover:shadow-accent/40 transform hover:-translate-y-1 hover:scale-102">
        <Image
          src={imageUrl || "/placeholder.svg"}
          alt={genre.name || "Genre"}
          fill
          className="object-cover transition-transform duration-300 group-hover:scale-110"
          data-ai-hint={imageHint}
          sizes="(max-width: 768px) 50vw, (max-width: 1200px) 25vw, 16.6vw"
        />
        <div className="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent" />
        <div className="absolute bottom-0 left-0 p-4">
          <h3 className="font-bold text-white text-lg">{genre.name}</h3>
        </div>
      </Card>
    </Link>
  )
}
