"use client";

import React from "react";

interface PosterImageProps extends React.ImgHTMLAttributes<HTMLImageElement> {
  src: string;
  alt?: string;
}

export default function PosterImage({ src, alt = "poster", className, ...rest }: PosterImageProps) {
  const resolvedSrc = src || '/no_poster.png';

  const handleError = (e: React.SyntheticEvent<HTMLImageElement, Event>) => {
    const img = e.currentTarget as HTMLImageElement;
    console.warn('[PosterImage] failed to load', resolvedSrc);
    img.onerror = null;
    img.src = '/no_poster.png';
  };

  // log the src to help debug why images aren't shown
  React.useEffect(() => {
    console.debug('[PosterImage] src', resolvedSrc);
  }, [resolvedSrc]);

  return <img src={resolvedSrc} alt={alt} className={className} onError={handleError} {...rest} />;
}
